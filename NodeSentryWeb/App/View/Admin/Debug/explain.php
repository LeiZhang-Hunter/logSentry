<?php
include_once __VIEW__ . "Admin/css_header.php";
?>
<link rel="stylesheet" href="<?=site_url(__PUBLIC__."js/jsoneditor/jsoneditor.min.css")?>">
<style type="text/css">
    /* GitHub stylesheet for MarkdownPad (http://markdownpad.com) */
    /* Author: Nicolas Hery - http://nicolashery.com */
    /* Version: b13fe65ca28d2e568c6ed5d7f06581183df8f2ff */
    /* Source: https://github.com/nicolahery/markdownpad-github */

    /* RESET
    =============================================================================*/

    img{
        width: 100%;
    }

    /* BODY
    =============================================================================*/



    body>*:first-child {
        margin-top: 0 !important;
    }

    body>*:last-child {
        margin-bottom: 0 !important;
    }

    /* BLOCKS
    =============================================================================*/

    p, blockquote, ul, ol, dl, table, pre {
        margin: 15px 0;
    }

    /* HEADERS
    =============================================================================*/

    h1, h2, h3, h4, h5, h6 {
        margin: 20px 0 10px;
        padding: 0;
        font-weight: bold;
        -webkit-font-smoothing: antialiased;
    }

    h1 tt, h1 code, h2 tt, h2 code, h3 tt, h3 code, h4 tt, h4 code, h5 tt, h5 code, h6 tt, h6 code {
        font-size: inherit;
    }

    h1 {
        font-size: 28px;
        color: #000;
    }

    h2 {
        font-size: 24px;
        border-bottom: 1px solid #ccc;
        color: #000;
    }

    h3 {
        font-size: 18px;
    }

    h4 {
        font-size: 16px;
    }

    h5 {
        font-size: 14px;
    }

    h6 {
        color: #777;
        font-size: 14px;
    }

    body>h2:first-child, body>h1:first-child, body>h1:first-child+h2, body>h3:first-child, body>h4:first-child, body>h5:first-child, body>h6:first-child {
        margin-top: 0;
        padding-top: 0;
    }

    a:first-child h1, a:first-child h2, a:first-child h3, a:first-child h4, a:first-child h5, a:first-child h6 {
        margin-top: 0;
        padding-top: 0;
    }

    h1+p, h2+p, h3+p, h4+p, h5+p, h6+p {
        margin-top: 10px;
    }

    /* LINKS
    =============================================================================*/

    a {
        color: #4183C4;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    /* LISTS
    =============================================================================*/

    ul, ol {
        padding-left: 30px;
    }

    ul li > :first-child,
    ol li > :first-child,
    ul li ul:first-of-type,
    ol li ol:first-of-type,
    ul li ol:first-of-type,
    ol li ul:first-of-type {
        margin-top: 0px;
    }

    ul ul, ul ol, ol ol, ol ul {
        margin-bottom: 0;
    }

    dl {
        padding: 0;
    }

    dl dt {
        font-size: 14px;
        font-weight: bold;
        font-style: italic;
        padding: 0;
        margin: 15px 0 5px;
    }

    dl dt:first-child {
        padding: 0;
    }

    dl dt>:first-child {
        margin-top: 0px;
    }

    dl dt>:last-child {
        margin-bottom: 0px;
    }

    dl dd {
        margin: 0 0 15px;
        padding: 0 15px;
    }

    dl dd>:first-child {
        margin-top: 0px;
    }

    dl dd>:last-child {
        margin-bottom: 0px;
    }

    /* CODE
    =============================================================================*/

    pre, code, tt {
        font-size: 12px;
        font-family: Consolas, "Liberation Mono", Courier, monospace;
    }

    code, tt {
        margin: 0 0px;
        padding: 0px 0px;
        white-space: nowrap;
        border: 1px solid #eaeaea;
        background-color: #f8f8f8;
        border-radius: 3px;
    }

    pre>code {
        margin: 0;
        padding: 0;
        white-space: pre;
        border: none;
        background: transparent;
    }

    pre {
        background-color: #f8f8f8;
        border: 1px solid #ccc;
        font-size: 13px;
        line-height: 19px;
        overflow: auto;
        padding: 6px 10px;
        border-radius: 3px;
    }

    pre code, pre tt {
        background-color: transparent;
        border: none;
    }

    kbd {
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background-color: #DDDDDD;
        background-image: linear-gradient(#F1F1F1, #DDDDDD);
        background-repeat: repeat-x;
        border-color: #DDDDDD #CCCCCC #CCCCCC #DDDDDD;
        border-image: none;
        border-radius: 2px 2px 2px 2px;
        border-style: solid;
        border-width: 1px;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        line-height: 10px;
        padding: 1px 4px;
    }

    /* QUOTES
    =============================================================================*/

    blockquote {
        border-left: 4px solid #DDD;
        padding: 0 15px;
        color: #777;
    }

    blockquote>:first-child {
        margin-top: 0px;
    }

    blockquote>:last-child {
        margin-bottom: 0px;
    }

    /* HORIZONTAL RULES
    =============================================================================*/

    hr {
        clear: both;
        margin: 15px 0;
        height: 0px;
        overflow: hidden;
        border: none;
        background: transparent;
        border-bottom: 4px solid #ddd;
        padding: 0;
    }

    /* TABLES
    =============================================================================*/

    table th {
        font-weight: bold;
    }

    table th, table td {
        border: 1px solid #ccc;
        padding: 6px 13px;
    }

    table tr {
        border-top: 1px solid #ccc;
        background-color: #fff;
    }

    table tr:nth-child(2n) {
        background-color: #f8f8f8;
    }

    /* IMAGES
    =============================================================================*/

    img {
        max-width: 100%
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
<!-- Main Header -->
<div id="header">
    <?php
    include_once __VIEW__ . "Admin/header.php";
    ?>
</div>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <?php
    include_once __VIEW__ . "Admin/left.php";
    ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper scroll_bar">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-file"></i> 调试中心</a></li>
                <li>社区谷歌插件下载</li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">社区谷歌插件下载</h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <h1>插件使用说明:</h1>
                    <h2>1.浏览器端：</h2>
                    <h4>修改myscript.js中的配置文件</h4>
                    <pre><code>将client_id 修改位账号 ，将 server_ip修改为与之通讯的服务端ip，将server_port修改为与之通讯的服务端端口
</code></pre>

                    <h4>插件属于谷歌浏览器，不能使用在其他以外的浏览器上，注意这是一个异步调试工具：</h4>
                    <h4>首先我们需要在谷歌浏览器的右上角中点击</h4>
                    <pre><code>更多工具====》扩展程序=====》然后点击左上角中的加载以解压的扩展程序
</code></pre>

                    <h4>具体如图</h4>
                    <p><img src="<?=site_url("/Public/img/chrome_extension/extension1_step/step1.png")?>" /></p>
                    <p><img src="<?=site_url("/Public/img/chrome_extension/extension1_step/step2.png")?>" /></p>
                    <h4>然后查看谷歌浏览器右上方是否出现扩展</h4>
                    <p><img src="<?=site_url("/Public/img/chrome_extension/extension1_step/step3.png")?>" /></p>
                    <h2>2.服务端调试调用:</h2>
                    <pre><code>&lt;?php
Console::debug(&quot;hello swoole&quot;);
</code></pre>

                    <h2>3.浏览器端输出</h2>
                    <p><img src="<?=site_url("/Public/img/chrome_extension/extension1_step/step4.png")?>" /></p>
                </div>

            </div>
        </div>
    </div>

    <!-- /.content-wrapper -->
</div>


<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<script src="<?=site_url(__PUBLIC__."js/common/commonQuery.js")?>"></script>
<script src="<?=site_url(__PUBLIC__."js/common/extensionObject.js")?>"></script>

<!-- AdminLTE App -->
<!--<script src="js/app.js"></script>-->
<script>
    //插件下载
    $("#debug_manage").addClass("active");
    $("#debug_explain").addClass("active");
</script>
</body>
</html>

