<?php
/**
 * The Flycorn cms
 * Author: flycorn
 * Email: yuming@flycorn.com
 * Date: 15/9/16
 */
class EmailLibrary extends Container
{

    private $ci = null;

    private $config = array(); //邮箱配置

    private $admin_conf = array(); //后台配置

    public function __construct($conf = array())
    {
        $this -> ci = &get_instance();

        $this -> Init($conf);
    }

    //初始化
    private function Init($conf)
    {
        $this -> admin_conf = $conf;

        //邮件配置
        $config = array();
        $config['protocol'] = isset($conf['site_email_protocol']) ? $conf['site_email_protocol'] : ''; //协议
        $config['smtp_host'] = isset($conf['site_email_host']) ? $conf['site_email_host'] : ''; //服务器
        $config['smtp_user'] = isset($conf['site_email_name']) ? $conf['site_email_name'] : ''; //账号
        $config['smtp_pass'] = isset($conf['site_email_pwd']) ? $conf['site_email_pwd'] : ''; //密码
        $config['mailtype'] = "html";
        $config['validate'] = true;
        $config['priority'] = 1;
        $config['crlf'] = '\r\n';
        $config['smtp_port'] = 25;
        $config['wordwrap'] = TRUE;
        $this -> config = $config;
    }

    /**
     * 发送邮件
     * @param string $email
     * @param string $content
     */
    public function Send($to_email = '',  $title = '', $content = '')
    {
        $from_email = $this -> admin_conf['site_email_name'];

        $this -> ci -> load -> library('email', $this -> config);
        $this -> ci -> email -> from($from_email, $this -> admin_conf['site_email_nickname']);//发件人
        $this -> ci -> email -> to($to_email);
        $this -> ci -> email -> subject($title);
        $this -> ci -> email -> message($content);
        $result = $this -> ci -> email -> send();

        //判断是否发送成功
        if($result){
            return true;
        }
        return false;
    }

}