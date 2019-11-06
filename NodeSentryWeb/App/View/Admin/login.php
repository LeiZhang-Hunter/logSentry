
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?=site_url(__PUBLIC__."img/ico/favicon.ico")?>">

    <title>日志管理 V1.0</title>

    <!-- Ploceidae core CSS -->
    <link href="<?=site_url(__PUBLIC__."css/ploceidae.css")?>" rel="stylesheet">
    <!-- perfect-scrollbar CSS -->
    <link href="<?=site_url(__PUBLIC__."css/perfect-scrollbar.css")?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>-->
    <script src="<?=site_url(__PUBLIC__."js/html5shiv.min.js")?>"></script>
    <script src="<?=site_url(__PUBLIC__."js/respond.min.js")?>"></script>
    <![endif]-->
</head>

<body class="bg-gray">
<div class="content-wrapper scroll_bar login">
    <div class="login-wrapper">
        <div class="login-input">
            <dl class="text-center">
                <dt><img src="<?=site_url(__PUBLIC__."img/logo.png")?>"></dt>
                <dd><h2>swoole-syslog</h2></dd>
            </dl>
            <form class="form-v">
                <div class="form-group">
                    <input type="text" value="test" name="username" class="form-control" placeholder="请输入用户名">
                </div>
                <div class="form-group">
                    <input type="password" value="test" name="password" class="form-control" placeholder="请输入密码">
                </div>
                <button type="button" style="background:#287ef9;border-color:#287ef9" id="login" class="btn btn-block">登 录</button>
            </form>
        </div>
    </div>
    <div class="login-bottom text-center">
        <div class="login-wrapper">

        </div>
    </div>
</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?=site_url(__PUBLIC__."js/jquery.min.js")?>"></script>
<!-- plugins -->
<script src="<?=site_url(__PUBLIC__."js/plugins/transition.js")?>"></script>
<script src="<?=site_url(__PUBLIC__."js/plugins/tooltip.js")?>"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?=site_url(__PUBLIC__."js/ie10-viewport-bug-workaround.js")?>"></script>
<!-- scrollbar -->
<script src="<?=site_url(__PUBLIC__."js/scrollbar/jquery.mousewheel.js")?>"></script>
<script src="<?=site_url(__PUBLIC__."js/scrollbar/perfect-scrollbar.js")?>"></script>
<!-- 表单验证 -->
<script src="<?=site_url(__PUBLIC__."js/plugins/validation/jquery.validate.js")?>"></script>
<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<script type="text/javascript">
    // scrollbar 滚动条
    //------------------------------------------
    jQuery(document).ready(function ($) {
        "use strict";
        $('.scroll_bar').perfectScrollbar();
    });

    $("#login").click(function(){
        var username = $("input[name='username']").val();
        var password = $("input[name='password']").val();
        $.post("<?=base_url("/Index/login")?>",{username:username,password:password},function (response) {
            if(+response.code === 0)
            {
                window.location.href = "<?=base_url("/Admin/Index/index")?>"
            }else{
                $.waring(response.response);
            }
        },"json");
    });
</script>
<!-- AdminLTE App -->
<script src="js/app.js"></script>
</body>
</html>
