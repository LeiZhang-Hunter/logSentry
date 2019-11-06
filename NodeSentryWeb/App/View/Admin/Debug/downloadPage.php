<?php
include_once __VIEW__ . "Admin/css_header.php";
?>
<link rel="stylesheet" href="<?=site_url(__PUBLIC__."js/jsoneditor/jsoneditor.min.css")?>">


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
                    <!-- form start -->
                    <div class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">插件：</label>
                                <ul class="list-unstyled img-grid pic-sorting dad-active dad-container">
                                    <li class="item item1 dads-children" data-dad-id="1" data-dad-position="1">
                                        <a href="javascript:;" class="img hint hint-top" data-hint="点击更换图片" role="button" data-toggle="modal" data-target="#content-main"><img src="<?=site_url("/Public/img/chrome_extension/chelun_community.png")?>" class="img-responsive"></a>
                                    </li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <div onclick="extensionObject.download($(this),'<?=base_url("/Admin/Debug/download")?>')"  class="col-sm-10 col-sm-offset-2">
                                    <button id="download" type="button" class="btn btn-info btn-sm">插件下载</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
    $("#debug_download").addClass("active");
</script>
</body>
</html>

