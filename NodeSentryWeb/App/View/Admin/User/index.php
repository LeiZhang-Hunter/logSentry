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
                <li class="active"> 成员列表</li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">成员列表</h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                            <a href="<?=base_url("/Admin/User/add")?>" class="btn btn-primary btn-sm"><i class="icon-circle"></i> 添加成员</a>
                        </div>
                    </div>
                </div>

                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="mailbox-controls">
<!--                        <form class="form-inline">-->
<!--                            -->
<!--                            <div class="form-group">-->
<!--                                <label>发布时间：</label>-->
<!--                                <input type="text" class="form-control input-sm datepicker" placeholder="请选择发布时间"> --->
<!--                                <input type="text" class="form-control input-sm datepicker" placeholder="请选择发布时间">-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <label>关键字：</label>-->
<!--                                <input type="text" class="form-control input-sm" placeholder="请输入关键字">-->
<!--                            </div>-->
<!--                            <a href="javascript:;" role="button" class="btn btn-primary btn-sm"><i class="icon-magnifier"></i> 搜索</a>-->
<!--                        </form>-->
                    </div>
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                            <tr>
                                <th width="5%">编号</th>
                                <th class="text-left">用户名</th>
                                <th class="text-left">真实姓名</th>
                                <th class="text-center">开户状态</th>
                                <th class="text-left">注册日期</th>
                                <th class="text-right pad25">操作</th>
                            </tr>

                            <?php if($list){ ?>
                                <?php foreach ($list as $key=>$value){ ?>
                                <tr>
                                    <td><label><input type="checkbox" name='batchCheckBox'> <?=$value["id"]?></label></td>
                                    <td class="text-left">
                                        <div class="table-content">
                                            <img src="<?=site_url("/Public/img/header.png")?>" alt="头像">
                                            <div class="table-content-text">
                                                <?=$value["username"]?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <?=$value["true_name"]?>
                                    </td>
                                    <td class="text-center">
                                        <i class="icon-right"> </i>
                                    </td>
                                    <td class="text-left">
                                        <?=$value["created_time"] ? date("Y-m-d H:i:s",$value["created_time"]) : ""?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?=base_url("/Admin/User/update?id=".$value["id"])?>" class="btn btn-success btn-sm"><i class="icon-tools"></i> 修改</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>

                            </tbody>
                        </table>
                        <!-- /.table -->
                    </div>
                    <!-- /.mail-box-messages -->
                </div>
                <div class="box-footer clearfix">
<!--                    <a href="javascript:;" class="mailbox-messages btn btn-sm checkbox-toggle" style="padding-left: 0px;">-->
<!--                        <label><input type="checkbox" id="cbAll" value="all"> 全选</label>-->
<!--                    </a>-->
<!--                    <div class="btn-group" role="group">-->
<!--                        <a href="javascript:;" role="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="icon-trash"></i> 批量删除</a>-->
<!--                        <a href="javascript:;" role="button" class="btn btn-success btn-sm" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="icon-tools"></i> 正常</a>-->
<!--                        <a href="javascript:;" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="icon-add"></i> 停用</a>-->
<!--                    </div>-->
                    <ul class="pagination pagination-sm no-margin pull-right">
                        <?=$show?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- /.content-wrapper -->
</div>
<!-- Modal -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="mySmallModalLabel">提示</h5>
            </div>
            <div class="modal-body text-center">
                您确定要执行此操作？
            </div>
            <div class="modal-footer">
                <a href="javascript:;" role="button" class="btn btn-success btn-sm pull-left">确认</a>
                <a href="javascript:;" role="button" class="btn btn-warning btn-sm pull-right" data-dismiss="modal">放弃</a>
            </div>
        </div>
    </div>
</div>
<!-- Placed at the end of the document so the pages load faster -->
<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<script src="<?=site_url("/Public/js/plugins/datepicker/datepicker.js")?>"></script>
<script type="text/javascript">
    $("#user_list_menu").addClass("active");
    $("#system_config").addClass("active");
</script>
<!-- AdminLTE App -->
</body>
</html>
