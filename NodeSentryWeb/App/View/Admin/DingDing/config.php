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
                <li><a href="javascript:;"><i class="icon-crown"></i> 系统设置</a></li>
                <li><a href="javascript:;">钉钉配置</a></li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">钉钉配置设置</h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <form class="form-horizontal">
                        <div class="box-body">


                            <div class="form-group">
                                <label class="col-sm-2 control-label">钉钉配置：</label>
                                <div class="col-sm-10">
<!--                                    <textarea id="editor_id" name="content" style="width:100%;height:300px;"></textarea>-->
                                    <div  id="jsoneditor" style="width: 100%; height: 400px;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <button onclick="dingdingObject.set()" type="button" class="btn btn-info btn-sm">保存</button>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
</div>


<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<script src="<?=site_url(__PUBLIC__."js/common/commonQuery.js")?>"></script>
<script src="<?=site_url(__PUBLIC__."js/jsoneditor/jsoneditor.min.js")?>"></script>
<script src="<?=site_url(__PUBLIC__."js/common/dingdingObject.js")?>"></script>
<script type="text/javascript">
    // scrollbar 滚动条
    //------------------------------------------
    dingdingObject.initJsonEditor('jsoneditor',<?=json_encode($config)?>);
    dingdingObject.setUrl = "<?=base_url('/Admin/DingDing/set')?>"
    $("#system_config").addClass("active");
    $("#dingding_config_menu").addClass("active");
</script>
<!-- AdminLTE App -->
<!--<script src="js/app.js"></script>-->
</body>
</html>
