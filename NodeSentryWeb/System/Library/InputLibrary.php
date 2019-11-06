<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-19 0019
 * Time: 13:38
 */
class InputLibrary extends Container {

    private $_enable_xss = FALSE;

    protected $_never_allowed_str =	array(
        'document.cookie' => '[removed]',
        'document.write'  => '[removed]',
        '.parentNode'     => '[removed]',
        '.innerHTML'      => '[removed]',
        '-moz-binding'    => '[removed]',
        '<!--'            => '&lt;!--',
        '-->'             => '--&gt;',
        '<![CDATA['       => '&lt;![CDATA[',
        '<comment>'	  => '&lt;comment&gt;',
        '<%'              => '&lt;&#37;'
    );

    protected $_never_allowed_regex = array(
        'javascript\s*:',
        '(document|(document\.)?window)\.(location|on\w*)',
        'expression\s*(\(|&\#40;)', // CSS and IE
        'vbscript\s*:', // IE, surprise!
        'wscript\s*:', // IE
        'jscript\s*:', // IE
        'vbs\s*:', // IE
        'Redirect\s+30\d',
        "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
    );

    protected $_xss_hash;

    public $charset = 'UTF-8';


    public function get($index = NULL, $xss_clean = NULL)
    {
        return $this->_fetch_from_array($_GET, $index, $xss_clean);
    }

    public function post($index = NULL, $xss_clean = NULL){
        return $this->_fetch_from_array($_POST, $index, $xss_clean);
    }

    protected function _fetch_from_array(&$array, $index = NULL, $xss_clean = NULL)
    {
        is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

        // If $index is NULL, it means that the whole $array is requested
        isset($index) OR $index = array_keys($array);

        // allow fetching multiple keys at once
        if (is_array($index))
        {
            $output = array();
            foreach ($index as $key)
            {
                $output[$key] = $this->_fetch_from_array($array, $key, $xss_clean);
            }

            return $output;
        }

        if (isset($array[$index]))
        {
            $value = $array[$index];
        }
        elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) // Does the index contain array notation
        {
            $value = $array;
            for ($i = 0; $i < $count; $i++)
            {
                $key = trim($matches[0][$i], '[]');
                if ($key === '') // Empty notation will return the value as array
                {
                    break;
                }

                if (isset($value[$key]))
                {
                    $value = $value[$key];
                }
                else
                {
                    return NULL;
                }
            }
        }
        else
        {
            return NULL;
        }

        return ($xss_clean === TRUE)
            ? $this->xss_clean($value)
            : $value;
    }

    public function xss_clean($str, $is_image = FALSE)
    {
        // Is the string an array?
        if (is_array($str))
        {
            foreach ($str as $key => &$value)
            {
                $str[$key] = $this->xss_clean($value);
            }

            return $str;
        }

        // Remove Invisible Characters
        $str = remove_invisible_characters($str);

        /*
         * URL Decode
         *
         * Just in case stuff like this is submitted:
         *
         * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
         *
         * Note: Use rawurldecode() so it does not remove plus signs
         */
        if (stripos($str, '%') !== false)
        {
            do
            {
                $oldstr = $str;
                $str = rawurldecode($str);
                $str = preg_replace_callback('#%(?:\s*[0-9a-f]){2,}#i', array($this, '_urldecodespaces'), $str);
            }
            while ($oldstr !== $str);
            unset($oldstr);
        }

        /*
         * Convert character entities to ASCII
         *
         * This permits our tests below to work reliably.
         * We only convert entities that are within tags since
         * these are the ones that will pose security problems.
         */
        $str = preg_replace_callback("/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);
        $str = preg_replace_callback('/<\w+.*/si', array($this, '_decode_entity'), $str);

        // Remove Invisible Characters Again!
        $str = remove_invisible_characters($str);

        /*
         * Convert all tabs to spaces
         *
         * This prevents strings like this: ja	vascript
         * NOTE: we deal with spaces between characters later.
         * NOTE: preg_replace was found to be amazingly slow here on
         * large blocks of data, so we use str_replace.
         */
        $str = str_replace("\t", ' ', $str);

        // Capture converted string for later comparison
        $converted_string = $str;

        // Remove Strings that are never allowed
        $str = $this->_do_never_allowed($str);

        /*
         * Makes PHP tags safe
         *
         * Note: XML tags are inadvertently replaced too:
         *
         * <?xml
         *
         * But it doesn't seem to pose a problem.
         */
        if ($is_image === TRUE)
        {
            // Images have a tendency to have the PHP short opening and
            // closing tags every so often so we skip those and only
            // do the long opening tags.
            $str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
        }
        else
        {
            $str = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $str);
        }

        /*
         * Compact any exploded words
         *
         * This corrects words like:  j a v a s c r i p t
         * These words are compacted back to their correct state.
         */
        $words = array(
            'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
            'vbs', 'script', 'base64', 'applet', 'alert', 'document',
            'write', 'cookie', 'window', 'confirm', 'prompt', 'eval'
        );

        foreach ($words as $word)
        {
            $word = implode('\s*', str_split($word)).'\s*';

            // We only want to do this when it is followed by a non-word character
            // That way valid stuff like "dealer to" does not become "dealerto"
            $str = preg_replace_callback('#('.substr($word, 0, -3).')(\W)#is', array($this, '_compact_exploded_words'), $str);
        }

        /*
         * Remove disallowed Javascript in links or img tags
         * We used to do some version comparisons and use of stripos(),
         * but it is dog slow compared to these simplified non-capturing
         * preg_match(), especially if the pattern exists in the string
         *
         * Note: It was reported that not only space characters, but all in
         * the following pattern can be parsed as separators between a tag name
         * and its attributes: [\d\s"\'`;,\/\=\(\x00\x0B\x09\x0C]
         * ... however, remove_invisible_characters() above already strips the
         * hex-encoded ones, so we'll skip them below.
         */
        do
        {
            $original = $str;

            if (preg_match('/<a/i', $str))
            {
                $str = preg_replace_callback('#<a(?:rea)?[^a-z0-9>]+([^>]*?)(?:>|$)#si', array($this, '_js_link_removal'), $str);
            }

            if (preg_match('/<img/i', $str))
            {
                $str = preg_replace_callback('#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si', array($this, '_js_img_removal'), $str);
            }

            if (preg_match('/script|xss/i', $str))
            {
                $str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
            }
        }
        while ($original !== $str);
        unset($original);

        /*
         * Sanitize naughty HTML elements
         *
         * If a tag containing any of the words in the list
         * below is found, the tag gets converted to entities.
         *
         * So this: <blink>
         * Becomes: &lt;blink&gt;
         */
        $pattern = '#'
            .'<((?<slash>/*\s*)((?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)|.+)' // tag start and name, followed by a non-tag character
            .'[^\s\042\047a-z0-9>/=]*' // a valid attribute character immediately after the tag would count as a separator
            // optional attributes
            .'(?<attributes>(?:[\s\042\047/=]*' // non-attribute characters, excluding > (tag close) for obvious reasons
            .'[^\s\042\047>/=]+' // attribute characters
            // optional attribute-value
            .'(?:\s*=' // attribute-value separator
            .'(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))' // single, double or non-quoted value
            .')?' // end optional attribute-value group
            .')*)' // end optional attributes group
            .'[^>]*)(?<closeTag>\>)?#isS';

        // Note: It would be nice to optimize this for speed, BUT
        //       only matching the naughty elements here results in
        //       false positives and in turn - vulnerabilities!
        do
        {
            $old_str = $str;
            $str = preg_replace_callback($pattern, array($this, '_sanitize_naughty_html'), $str);
        }
        while ($old_str !== $str);
        unset($old_str);

        /*
         * Sanitize naughty scripting elements
         *
         * Similar to above, only instead of looking for
         * tags it looks for PHP and JavaScript commands
         * that are disallowed. Rather than removing the
         * code, it simply converts the parenthesis to entities
         * rendering the code un-executable.
         *
         * For example:	eval('some code')
         * Becomes:	eval&#40;'some code'&#41;
         */
        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            '\\1\\2&#40;\\3&#41;',
            $str
        );

        // Final clean up
        // This adds a bit of extra precaution in case
        // something got through the above filters
        $str = $this->_do_never_allowed($str);

        /*
         * Images are Handled in a Special Way
         * - Essentially, we want to know that after all of the character
         * conversion is done whether any unwanted, likely XSS, code was found.
         * If not, we return TRUE, as the image is clean.
         * However, if the string post-conversion does not matched the
         * string post-removal of XSS, then it fails, as there was unwanted XSS
         * code found and removed/changed during processing.
         */
        if ($is_image === TRUE)
        {
            return ($str === $converted_string);
        }

        return $str;
    }


    /**
     * Do Never Allowed
     *
     * @used-by	CI_Security::xss_clean()
     * @param 	string
     * @return 	string
     */
    protected function _do_never_allowed($str)
    {
        $str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);

        foreach ($this->_never_allowed_regex as $regex)
        {
            $str = preg_replace('#'.$regex.'#is', '[removed]', $str);
        }

        return $str;
    }

    /**
     * Attribute Conversion
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$match
     * @return	string
     */
    protected function _convert_attribute($match)
    {
        return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
    }

    /**
     * HTML Entity Decode Callback
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$match
     * @return	string
     */
    protected function _decode_entity($match)
    {
        // Protect GET variables in URLs
        // 901119URL5918AMP18930PROTECT8198
        $match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $this->xss_hash().'\\1=\\2', $match[0]);

        // Decode, then un-protect URL GET vars
        return str_replace(
            $this->xss_hash(),
            '&',
            $this->entity_decode($match, $this->charset)
        );
    }

    /**
     * HTML Entities Decode
     *
     * A replacement for html_entity_decode()
     *
     * The reason we are not using html_entity_decode() by itself is because
     * while it is not technically correct to leave out the semicolon
     * at the end of an entity most browsers will still interpret the entity
     * correctly. html_entity_decode() does not convert entities without
     * semicolons, so we are left with our own little solution here. Bummer.
     *
     * @link	http://php.net/html-entity-decode
     *
     * @param	string	$str		Input
     * @param	string	$charset	Character set
     * @return	string
     */
    public function entity_decode($str, $charset = NULL)
    {
        if (strpos($str, '&') === FALSE)
        {
            return $str;
        }

        static $_entities;

        isset($charset) OR $charset = $this->charset;
        $flag = is_php('5.4')
            ? ENT_COMPAT | ENT_HTML5
            : ENT_COMPAT;

        if ( ! isset($_entities))
        {
            $_entities = array_map('strtolower', get_html_translation_table(HTML_ENTITIES, $flag, $charset));

            // If we're not on PHP 5.4+, add the possibly dangerous HTML 5
            // entities to the array manually
            if ($flag === ENT_COMPAT)
            {
                $_entities[':'] = '&colon;';
                $_entities['('] = '&lpar;';
                $_entities[')'] = '&rpar;';
                $_entities["\n"] = '&NewLine;';
                $_entities["\t"] = '&Tab;';
            }
        }

        do
        {
            $str_compare = $str;

            // Decode standard entities, avoiding false positives
            if (preg_match_all('/&[a-z]{2,}(?![a-z;])/i', $str, $matches))
            {
                $replace = array();
                $matches = array_unique(array_map('strtolower', $matches[0]));
                foreach ($matches as &$match)
                {
                    if (($char = array_search($match.';', $_entities, TRUE)) !== FALSE)
                    {
                        $replace[$match] = $char;
                    }
                }

                $str = str_replace(array_keys($replace), array_values($replace), $str);
            }

            // Decode numeric & UTF16 two byte entities
            $str = html_entity_decode(
                preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', $str),
                $flag,
                $charset
            );

            if ($flag === ENT_COMPAT)
            {
                $str = str_replace(array_values($_entities), array_keys($_entities), $str);
            }
        }
        while ($str_compare !== $str);
        return $str;
    }

    /**
     * XSS Hash
     *
     * Generates the XSS hash if needed and returns it.
     *
     * @see		CI_Security::$_xss_hash
     * @return	string	XSS hash
     */
    public function xss_hash()
    {
        if ($this->_xss_hash === NULL)
        {
            $rand = $this->get_random_bytes(16);
            $this->_xss_hash = ($rand === FALSE)
                ? md5(uniqid(mt_rand(), TRUE))
                : bin2hex($rand);
        }

        return $this->_xss_hash;
    }

    /**
     * Get random bytes
     *
     * @param	int	$length	Output length
     * @return	string
     */
    public function get_random_bytes($length)
    {
        if (empty($length) OR ! ctype_digit((string) $length))
        {
            return FALSE;
        }

        if (function_exists('random_bytes'))
        {
            try
            {
                // The cast is required to avoid TypeError
                return random_bytes((int) $length);
            }
            catch (Exception $e)
            {
                // If random_bytes() can't do the job, we can't either ...
                // There's no point in using fallbacks.
                productError($e->getMessage());
                return FALSE;
            }
        }

        // Unfortunately, none of the following PRNGs is guaranteed to exist ...
        if (defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== FALSE)
        {
            return $output;
        }


        if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE)
        {
            // Try not to waste entropy ...
            is_php('5.4') && stream_set_chunk_size($fp, $length);
            $output = fread($fp, $length);
            fclose($fp);
            if ($output !== FALSE)
            {
                return $output;
            }
        }

        if (function_exists('openssl_random_pseudo_bytes'))
        {
            return openssl_random_pseudo_bytes($length);
        }

        return FALSE;
    }

    /**
     * Compact Exploded Words
     *
     * Callback method for xss_clean() to remove whitespace from
     * things like 'j a v a s c r i p t'.
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$matches
     * @return	string
     */
    protected function _compact_exploded_words($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
    }

    /**
     * Sanitize Naughty HTML
     *
     * Callback method for xss_clean() to remove naughty HTML elements.
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$matches
     * @return	string
     */
    protected function _sanitize_naughty_html($matches)
    {
        static $naughty_tags    = array(
            'alert', 'area', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
            'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
            'iframe', 'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
            'plaintext', 'style', 'script', 'textarea', 'title', 'math', 'video', 'svg', 'xml', 'xss'
        );

        static $evil_attributes = array(
            'on\w+', 'style', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'
        );

        // First, escape unclosed tags
        if (empty($matches['closeTag']))
        {
            return '&lt;'.$matches[1];
        }
        // Is the element that we caught naughty? If so, escape it
        elseif (in_array(strtolower($matches['tagName']), $naughty_tags, TRUE))
        {
            return '&lt;'.$matches[1].'&gt;';
        }
        // For other tags, see if their attributes are "evil" and strip those
        elseif (isset($matches['attributes']))
        {
            // We'll store the already fitlered attributes here
            $attributes = array();

            // Attribute-catching pattern
            $attributes_pattern = '#'
                .'(?<name>[^\s\042\047>/=]+)' // attribute characters
                // optional attribute-value
                .'(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))' // attribute-value separator
                .'#i';

            // Blacklist pattern for evil attribute names
            $is_evil_pattern = '#^('.implode('|', $evil_attributes).')$#i';

            // Each iteration filters a single attribute
            do
            {
                // Strip any non-alpha characters that may precede an attribute.
                // Browsers often parse these incorrectly and that has been a
                // of numerous XSS issues we've had.
                $matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);

                if ( ! preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE))
                {
                    // No (valid) attribute found? Discard everything else inside the tag
                    break;
                }

                if (
                    // Is it indeed an "evil" attribute?
                    preg_match($is_evil_pattern, $attribute['name'][0])
                    // Or does it have an equals sign, but no value and not quoted? Strip that too!
                    OR (trim($attribute['value'][0]) === '')
                )
                {
                    $attributes[] = 'xss=removed';
                }
                else
                {
                    $attributes[] = $attribute[0][0];
                }

                $matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
            }
            while ($matches['attributes'] !== '');

            $attributes = empty($attributes)
                ? ''
                : ' '.implode(' ', $attributes);
            return '<'.$matches['slash'].$matches['tagName'].$attributes.'>';
        }

        return $matches[0];
    }
}