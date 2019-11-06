<?php include_once __VIEW__ . "Admin/css_header.php" ?>

<body class="hold-transition skin-blue sidebar-mini">
<!-- Main Header -->
<?php include_once __VIEW__ . "Admin/header.php" ?>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <?php include_once __VIEW__ . "Admin/left.php" ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper scroll_bar">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-tools"></i> 项目列表</a></li>
                <li class="active"><?=$title?></li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title"><?=$title?></h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                            <a href="<?=base_url("/Admin/Project/index")?>" class="btn btn-primary btn-sm"><i class="icon-return"></i> 返回</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group  has-feedback">
                                <label class="col-xs-2 control-label">用户账号：</label>
                                <div class="col-xs-10">
                                    <input id="username" value="<?=isset($info["username"]) ? $info["username"] : ""?>" class="form-control" type="text" placeholder="用户账号">
                                    <i class="icon-shutdown icon-sm form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-2 control-label">选择客户机：</label>
                                <div class="col-xs-10">
                                    <select class="form-control" name="client_id" aria-required="true" aria-invalid="true">
                                        <option value="">无</option>
                                        <?php foreach ($client_list as $client_key=>$client_value){ ?>
                                            <option <?php
                                                if(isset($info["client_id"]) && ($info["client_id"] == $client_value["id"]))
                                                {
                                                    echo "selected='selected'";
                                                }
                                            ?> value="<?=$client_value["id"]?>"><?=$client_value["client_ip"]?></option>
                                        <?php } ?>
                                    </select>
<!--                                    <input id="client_ip" value="--><?//=isset($info["client_ip"]) ? $info["client_ip"] : ""?><!--" class="form-control" type="text" placeholder="客户机ip地址">-->
                                    <i class="icon-shutdown icon-sm form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="form-group  has-feedback">
                                <label class="col-xs-2 control-label">项目名称：</label>
                                <div class="col-xs-10">
                                    <input id="project_name" value="<?=isset($info["project_name"]) ? $info["project_name"] : ""?>" class="form-control" type="text" placeholder="项目名称">
                                    <i class="icon-shutdown icon-sm form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="form-group  has-feedback">
                                <label class="col-xs-2 control-label">项目路径：</label>
                                <div class="col-xs-10">
                                    <input id="project_dir" value="<?=isset($info["project_dir"]) ? $info["project_dir"] : ""?>" class="form-control" type="text" placeholder="项目路径">
                                    <i class="icon-shutdown icon-sm form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-10 col-xs-offset-2">
                                    <button type="button" id="save" class="btn btn-info btn-sm">保存</button>
                                    <button type="button" class="btn btn-default btn-sm">重置</button>
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

<?php include_once __VIEW__ . "Admin/Admin/js_loader.php" ?>
<script src="<?=site_url(__PUBLIC__."js/common/projectObject.js")?>"></script>
<script type="text/javascript">
    // scrollbar 滚动条
    //------------------------------------------
    jQuery(document).ready(function ($) {
        "use strict";
        $('.scroll_bar').perfectScrollbar();
    });
    $(function () {
        // iCheck
        //------------------------------------------
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        // Tooltip
        //------------------------------------------
        $("[data-toggle='tooltip']").tooltip();
    });



    $("#project_list_menu").addClass("active");
    $("#log_manage").addClass("active");

    projectObject.make("<?=base_url("/Admin/Project/deal")?>","<?=base_url("/Admin/Project/index")?>");
</script>
<!-- AdminLTE App -->
</body>
</html>
