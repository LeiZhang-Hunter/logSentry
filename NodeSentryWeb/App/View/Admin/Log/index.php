<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?=site_url("/Public/img/ico/favicon.ico")?>">

    <title>日志管理系统</title>

    <!-- Ploceidae core CSS -->
    <link href="<?=site_url("/Public/css/ploceidae.css")?>" rel="stylesheet">
    <!-- perfect-scrollbar CSS -->
    <link href="<?=site_url("/Public/css/perfect-scrollbar.css")?>" rel="stylesheet">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=site_url("/Public/css/plugins/iCheck/flat/blue.css")?>">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>-->
    <script src="<?=site_url("/Public/js/html5shiv.min.js")?>"></script>
    <script src="<?=site_url("/Public/js/respond.min.js")?>"></script>
    <![endif]-->

    <!-- datepicker 普通日期选择器-->
    <link rel="stylesheet" href="<?=site_url("/Public/css/plugins/datepicker/datepicker3.css")?>">

</head>

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
                <li><a href="javascript:;"><i class="icon-tools"></i> 日志管理</a></li>
                <li class="active">日志管理</li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">日志列表</h5>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="mailbox-controls">
                        <form class="form-inline">


                            <div class="form-group">
                                <label>php日志级别：</label>
                                <select name="php_error" class="form-control input-sm">
                                    <option <?=FactoryController::get("type") === "" ? "selected='selected'" : ""?> value="">全部</option>
                                    <?php foreach (syslogModel::$php_error as $error_key=>$error_value){ ?>
                                        <option <?=FactoryController::get("php_error") === strval($error_key) ? "selected='selected'" : ""?>  value="<?=$error_key?>"><?=$error_value?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>ip地址：</label>
                                <input name="ip" value="<?=FactoryController::get("ip")?>" class="form-control input-sm" placeholder="请输入主机ip">
                            </div>
                            <div class="form-group">
                                <label>发生时间：</label>
                                <input name="begin_time" value="<?=FactoryController::get("begin_time")?>" type="text" class="form-control input-sm datepicker" placeholder="请选择发布时间"> -
                                <input name="end_time" value="<?=FactoryController::get("end_time")?>" type="text" class="form-control input-sm datepicker" placeholder="请选择发布时间">
                            </div>

                            <div class="form-group">
                                <label>消息体搜索：</label>
                                <input name="body" value="<?=FactoryController::get("body")?>" class="form-control input-sm" placeholder="请输入内容">
                            </div>

                            <a id="search" href="javascript:;" role="button" class="btn btn-primary btn-sm"><i class="icon-magnifier"></i> 搜索</a>
                        </form>
                    </div>
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                            <tr>
                                <th class="length-xs">编号</th>
                                <th>php错误级别</th>
<!--                                <th class="text-center">严重性</th>-->
                                <th class="text-center">ip地址</th>
                                <th class="text-center">内容</th>
                                <th class="text-center">发生时间</th>
                                <th  class="length-sm text-center">操作</th>
                            </tr>

                            <?php if($list){ ?>
                                <?php foreach ($list as $key=>$value){ ?>
                            <tr>
                                <td><label><input type="checkbox"> <?=$value["sys_id"]?></label></td>
                                <td><a style="color:<?=syslogModel::getErrorLevelColor($value["php_error_level"])?>" href="javascript:;" title="错误级别"><?=$value["php_error_level"]?></a> </td>
<!--                                <td class="text-center">--><?//=$value["level"]?><!--</td>-->
                                <td class="text-center"><?=$value["server_ip"]?></td>
                                <td class="text-center"><?=$value["body"]?></td>
                                <td class="text-center">
                                    <?=$value["happen_time"]?>
                                </td>
                                <td class="text-center">
                                    <a href="<?=site_url("/Admin/LogList/sysInfo")."?id=".$value["sys_id"]."&project_id=".(int)FactoryController::get("project_id")?>" class="btn btn-success btn-sm"><i class="icon-tools"></i> 查看</a>
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
                    <ul class="pagination pagination-sm no-margin pull-right">
                        <?=$show?>
                    </ul>
                    <div id="example"></div>
                </div>
                <script type='text/javascript'>

                </script>
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
            <div class="modal-body">
                您确定要执行此操作？
            </div>
            <div class="modal-footer">
                <a href="javascript:;" role="button" class="btn btn-warning btn-sm">取消</a>
                <a href="javascript:;" role="button" class="btn btn-success btn-sm">确认</a>
            </div>
        </div>
    </div>
</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?=site_url("/Public/js/jquery.min.js")?>"></script>
<!-- plugins -->
<script src="<?=site_url("/Public/js/plugins/transition.js")?>"></script>
<script src="<?=site_url("/Public/js/plugins/dropdown.js")?>"></script>
<script src="<?=site_url("/Public/js/plugins/tab.js")?>"></script>
<script src="<?=site_url("/Public/js/plugins/tooltip.js")?>"></script>
<script src="<?=site_url("/Public/js/plugins/modal.js")?>"></script>
<script src="<?=site_url("/Public/js/plugins/iCheck/icheck.min.js")?>"></script>
<!--<script src="--><?//=site_url("/Public/js/plugins/bootstrap-paginator.js")?><!--"></script>-->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?=site_url("/Public/js/ie10-viewport-bug-workaround.js")?>"></script>
<!-- scrollbar -->
<script src="<?=site_url("/Public/js/scrollbar/jquery.mousewheel.js")?>"></script>
<script src="<?=site_url("/Public/js/scrollbar/perfect-scrollbar.js")?>"></script>

<script src="<?=site_url("/Public/js/plugins/datepicker/datepicker.js")?>"></script>

<script type="text/javascript">
    // scrollbar 滚动条
    //------------------------------------------
    jQuery(document).ready(function ($) {
        "use strict";
        $('.scroll_bar').perfectScrollbar();
    });


    $("#search").click(function () {
         // var type= $("select[name='type']").val();
         var ip = $("input[name='ip']").val();
         var body = $("input[name='body']").val();
         var begin_time = $("input[name='begin_time']").val();
         var end_time = $("input[name='end_time']").val();
         var php_error = $("select[name='php_error']").val();
         //window.location.href = "<?//=site_url("/Admin/LogList/logCollect")?>//?type="+type+"&ip="+ip+"&body="+body+"&begin_time="+begin_time+"&end_time="+end_time+"&php_error="+php_error;
        window.location.href = "<?=site_url("/Admin/LogList/logCollect")?>?"+"&ip="+ip+"&body="+body+"&begin_time="+begin_time+"&end_time="+end_time+"&php_error="+php_error+"&project_id=<?=(int)FactoryController::get("project_id")?>";
    });

    //Date range picker with time picker（普通日期选择器）
    $('.datepicker').datepicker({
        autoclose: true
    });
    $("#log_manage").addClass("active");
    $("#log_list_menu").addClass("active");
</script>
<!-- AdminLTE App -->
<script src="<?=site_url("/Public/js/app.js")?>"></script>
</body>
</html>
