<?php include_once __VIEW__ . "Admin/css_header.php" ?>
<link rel="stylesheet" href="<?=site_url(__PUBLIC__."js/jsoneditor/jsoneditor.min.css")?>">
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
                <li><a href="javascript:;"><i class="icon-tools"></i> 服务列表</a></li>
                <li class="active"><?=$title?></li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title"><?=$title?></h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                            <a href="<?=base_url("/Admin/Service/index")?>" class="btn btn-primary btn-sm"><i class="icon-return"></i> 返回</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group  has-feedback">
                                <label class="col-xs-2 control-label">哨兵ip：</label>
                                <div class="col-xs-10">
                                    <input id="client_ip" value="<?=isset($info["client_ip"]) ? $info["client_ip"] : ""?>" class="form-control" type="text" placeholder="服务ip地址">
                                    <i class="icon-shutdown icon-sm form-control-feedback"></i>
                                </div>
                            </div>



                            <!-- /.box-header -->
                            <div class="form-group box-body no-padding">
                                <form class="form-horizontal">
                                    <div class="box-body">


                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">哨兵配置：</label>
                                            <div class="col-sm-10">
                                                <!--                                    <textarea id="editor_id" name="content" style="width:100%;height:300px;"></textarea>-->
                                                <div  id="jsoneditor" style="width: 100%; height: 400px;"></div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                </form>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-2 control-label">备注：</label>
                                <div class="col-xs-10">
                                    <textarea id="description"  class="form-control" rows="5" placeholder="请输入描述信息"><?=isset($info["description"]) ? trim($info["description"]) : ""?></textarea>
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

<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<script src="<?=site_url(__PUBLIC__."js/jsoneditor/jsoneditor.min.js")?>"></script>
<script src="<?=site_url(__PUBLIC__."js/common/configObject.js")?>"></script>
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

    $("#save").click(function(){
        var data = {};
        var client_ip = $("#client_ip").val();
        if(client_ip === "")
        {
            $.waring("请输入客户机ip地址");
            return false;
        }
        data.client_ip = client_ip;
        var description = $("#description").val();
        if(description === "")
        {
            $.waring("请输入客户机描述");
            return false;
        }
        data.description = description;

        var id = $.getParam("id");
        if(id)
        {
            data.id = id;
        }

        if(!/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(data.client_ip))
        {
            $.waring("客户机Ip地址输入不合法");
            return false;
        }

        var json = configObject.getObject().get();
        if(JSON.stringify(json) === "{}")
        {
            $.waring("请输入配置");
            return false;
        }


        $.post("<?=base_url("/Admin/Service/deal")?>",data,function(response){
            if(+response.code === 0)
            {
                window.location.href = "<?=base_url("/Admin/Service/index")?>";
            }else{
                $.waring(response.response);
                return false;
            }
        },"json");
    });

    $("#sentry_manage").addClass("active");
    $("#server_list_menu").addClass("active");
    configObject.initJsonEditor('jsoneditor',{});
</script>
<!-- AdminLTE App -->
</body>
</html>
