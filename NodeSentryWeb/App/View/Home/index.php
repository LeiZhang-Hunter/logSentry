<?php
include_once __VIEW__ . "Admin/css_header.php";
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper" style="top:0px">
    <div id="left_intelligent"></div>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="margin-left: 0px">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-basic"></i> 社区办公系统入口</a></li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">社区办公系统入口</h5>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="mailbox-controls">

                    </div>
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                            <tr>
                                <th>编号</th>
                                <th>名称</th>
                                <th class="text-center">操作</th>
                            </tr>

                            <?php foreach (Index::$backList as $key=>$value){ ?>
                            <tr>
                                <td><?=$key?></td>
                                <td><?=$value["name"]?></td>
                                <td class="text-center">
                                    <a target="_blank" href="<?=$value["url"]?>" role="button" class="btn btn-info btn-sm">查看</a>
                                </td>
                            </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                        <!-- /.table -->
                    </div>
                    <!-- /.mail-box-messages -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
</div>


<?php
include_once __VIEW__ . "Admin/js_loader.php";
?>


</body>
</html>

