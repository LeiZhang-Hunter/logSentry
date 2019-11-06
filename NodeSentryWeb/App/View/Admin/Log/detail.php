
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?=site_url("/Public/img/ico/favicon.ico")?>">

    <title>日志管理系统</title>

    <!-- Ploceidae core CSS -->
    <link href="<?=site_url("/Public/css/ploceidae.css")?>" rel="stylesheet">
    <!-- perfect-scrollbar CSS -->
    <link href="<?=site_url("/Public/css/perfect-scrollbar.css")?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>-->
    <script src="<?=site_url("/Public/js/html5shiv.min.js")?>"></script>
    <script src="<?=site_url("/Public/js/respond.min.js")?>"></script>
    <![endif]-->
</head>

<body class="hold-transition skin-blue sidebar-mini">
<!-- Main Header -->
<div id="header">
    <?php include __VIEW__ . "header.php"; ?>
</div>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <div id="left">
        <?php include __VIEW__ . "left.php"; ?>
    </div>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper scroll_bar order-detail">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-tools"></i> 日志管理</a></li>
                <li class="active">日志详情</li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">日志详情</h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                            <a href="<?=site_url("/Admin/LogList/logCollect")."?project_id=".(int)FactoryController::get("project_id")?>" class="btn btn-primary btn-sm"><i class="icon-jump"></i> 返回</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="order-detail-table">
                        <!--订单基本信息-->
                        <div class="table-bot">
                            <h5><b>日志基本信息：</b></h5>
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>日志编号</th>
                                    <th>日志设施</th>
                                    <th>日志严重性</th>
                                    <th>PHP错误级别</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?=isset($info["id"]) ? $info["id"] : ""?></td>
                                    <td><?=isset($info["facility"]) ? $info["facility"] : ""?></td>
                                    <td><?=isset($info["level"]) ? $info["level"] : ""?></td>
                                    <td style="color: <?=syslogModel::getErrorLevelColor($info["php_error_level"])?>"><?=$info["php_error_level"]?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-bot">
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>服务器ip</th>
                                    <th>服务器名称</th>
                                    <th>发生时间</th>
                                    <th>创建时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?=isset($info["server_ip"]) ? $info["server_ip"] : ""?></td>
                                    <td><?=isset($info["hostname"]) ? $info["hostname"] : ""?></td>
                                    <td><?=isset($info["happen_time"]) ? $info["happen_time"] : ""?></td>
                                    <td><?=isset($info["created_time"]) ? $info["created_time"] : ""?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-bot">
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>消息体</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?=isset($info["body"]) ? $info["body"] : ""?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- <div class="table-bot">
                            <h5><b>订单操作：</b></h5>
                            <div class="order-operating">
                                <form id="signupForm" method="post" class="form-horizontal" action="">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label font-noraml">修改订单状态：</label>
                                        <div class="col-sm-10 customer">
                                            <select class="form-control">
                                            	<option>全部</option>
                                                <option>待支付</option>
                                                <option>交易成功</option>
                                                <option>交易关闭</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label font-noraml">操作备注：</label>
                                        <div class="col-sm-10 customer">
                                            <textarea class="form-control" placeholder="填写备注信息"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">修改</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> -->
                        <!--管理员操作日志-->
                        <!-- <div class="table-bot">
                            <h5><b>操作日志：</b></h5>
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>操作人</th>
                                    <th>操作时间</th>
                                    <th>订单状态</th>
                                    <th>备注信息</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Admin</td>
                                    <td>2016-04-28 13:46:50</td>
                                    <td>未确认</td>
                                    <td>已打电话和确认，立即支付</td>
                                </tr>
                                </tbody>
                            </table>
                        </div> -->
                    </div>
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
<script type="text/javascript">

    // scrollbar 滚动条
    //------------------------------------------
    jQuery(document).ready(function ($) {
        "use strict";
        $('.scroll_bar').perfectScrollbar();
    });

    $("#log_manage").addClass("active");
    $("#log_list_menu").addClass("active");
</script>
<!-- AdminLTE App -->
<script src="<?=site_url("/Public/js/app.js")?>"></script>
</body>
</html>
