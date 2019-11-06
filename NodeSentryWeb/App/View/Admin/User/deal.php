<?php include_once __VIEW__ . "Admin/css_header.php" ?>

<body class="hold-transition skin-blue sidebar-mini">
<!-- Main Header -->
<div id="header">
    <?php include_once __VIEW__ . "Admin/header.php" ?>
</div>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <div id="left">
        <?php include_once __VIEW__ . "Admin/left.php" ?>
    </div>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper scroll_bar">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-crown"></i> 成员管理</a></li>
                <li><a href="javascript:;">成员列表</a></li>
                <li class="active"><?=$title?></li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title"><?=$title?></h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                            <a href="<?=base_url("/Admin/User/index")?>" class="btn btn-primary btn-sm"><i class="icon-jump"></i> 返回</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group has-feedback">
                                <label class="col-sm-2 control-label"><span class="font-red">*&nbsp;</span>用户名：</label>
                                <div class="col-sm-10">
                                    <input value="<?=isset($info["username"]) ? $info["username"] : ""?>" class="form-control" type="text" name="username" placeholder="请输入用户名">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="font-red">*&nbsp;</span>密码：</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="password"  placeholder="请输入密码">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="font-red">*&nbsp;</span>真实名称：</label>
                                <div class="col-sm-10">
                                    <input class="form-control" value="<?=isset($info["true_name"]) ? $info["true_name"] : ""?>" type="text" name="true_name" placeholder="请输入真实名称">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <button type="button" id="save" class="btn btn-info btn-sm">保存</button>
                                    <button type="reset" class="btn btn-default btn-sm">重置</button>
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

<!-- pop window pic-add 选择图片弹出 -->
<div class="modal fade photo-box" id="photo-box" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">插入图片</h4>
            </div>
            <div id="photoPic"></div>
        </div>
    </div>
</div>

<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<script type="text/javascript">
    $("#user_list_menu").addClass("active");
    $("#system_config").addClass("active");

    //点击保存
    $("#save").click(function(){
        var layerIndex = layer.load();
        var data = {};
        data.id = <?=(int)FactoryController::get("id")?>;
        data.username = $("input[name='username']").val();
        data.password = $("input[name='password']").val();
        data.true_name = $("input[name='true_name']").val();
        $.ajax({
            url : '<?=base_url("/Admin/User/deal")?>',
            type : 'post',
            data :data,
            dataType : 'json',
            success : function( response ){
                if(+response.code === 0)
                {
                    window.location.href = '<?=base_url("/Admin/User/index")?>';
                }else{
                    $.waring(response.msg);
                }
                layer.close(layerIndex);
            },
            error : function(){
                $.waring("网络繁忙");
                layer.close(layerIndex);
            }
        });
        $.post("",data,function(){

        },"json");
    });
</script>
<!-- AdminLTE App -->
</body>
</html>
