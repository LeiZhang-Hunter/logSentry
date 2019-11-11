<?php include_once __VIEW__ . "Admin/css_header.php" ?>

<body class="hold-transition skin-blue sidebar-mini">
<!-- Main Header -->
<header class="main-header">
    <?php include_once __VIEW__ . "Admin/header.php" ?>
</header>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <?php include_once __VIEW__ . "Admin/left.php" ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper scroll_bar">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-tools"></i> agent管理</a></li>
                <li class="active">agent管理</li>
            </ol>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h5 class="box-title">agent管理(agent管理)</h5>
                    <div class="box-tools">
                        <div class="has-feedback">
                            <a href="<?=base_url("/Admin/Client/addClient")?>?server_id=<?=isset($_GET["server_id"]) ? get_instance()->input->get("server_id") : 0?>" class="btn btn-primary btn-sm"><i class="icon-circle"></i> 添加哨兵</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="mailbox-controls">
                        <form class="form-inline">
                            <div class="form-group">
                                <label>ip地址：</label>
                                <input value="<?=FactoryController::get("ip")?>" type="text" name="ip" class="form-control input-sm" placeholder="请输入ip地址">
                            </div>
                            <a id="search" href="javascript:;" role="button" class="btn btn-primary btn-sm"><i class="icon-magnifier"></i> 搜索</a>
                        </form>
                    </div>
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                            <tr>
                                <th class="length-xs">编号</th>
                                <th class="length-xs"></th>
                                <th class="length-xs">名字</th>
                                <th>服务器ip</th>
                                <th class="text-center">状态</th>
                                <th  class="length-sm text-center">操作</th>
                            </tr>

                            <?php foreach ($list as $key=>$value){ ?>
                                <tr>
                                    <td><label> <?=$value["id"]?></label></td>
                                    <td><img width="100" height="100" data-original="img/tibet-1.jpg" src="<?=base_url("/Public/img/temp/client.jpg")?>" alt="图片1"></td>
                                    <td><a href="javascript:;" title="ip地址"><?=$value["name"]?></a> </td>
                                    <td><a href="javascript:;" title="ip地址"><?=$value["sentry_ip"]?></a> </td>
                                    <?php if($value["state"] == 1){ ?>
                                        <td class="text-center"><a href="javascript:;"><i class="icon-sucess icon-sm font-green"></i></a></td>
                                    <?php }else{ ?>
                                        <td class="text-center"><a href="javascript:;"><i class="icon-close icon-sm font-red"></i></a></td>
                                    <?php } ?>
                                    <td class="text-center">
                                        <a href="<?=base_url("/Admin/Client/updateClient?id=".$value["id"])."&server_id=".$value["server_id"]?>" class="btn btn-success btn-sm"><i class="icon-tools"></i> 修改</a>
                                        <a delete_id="<?=$value["id"]?>" href="javascript:;" role="button" class="btn-delete btn btn-danger btn-sm" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="icon-trash"></i> 删除</a>
                                    </td>
                                </tr>
                            <?php } ?>


                            </tbody>
                        </table>
                        <!-- /.table -->
                    </div>
                    <!-- /.mail-box-messages -->
                </div>
                <div class="box-footer clearfix">
                    <!-- Check all button -->
                    <!--                    <div class="btn-group" role="group">-->
                    <!--                        <a href="javascript:;" class="btn btn-danger btn-sm">批量删除</a>-->
                    <!--                    </div>-->
                    <!--<ul class="pagination pagination-sm no-margin pull-right">-->
                    <!--<li class="disabled"><a href="javascript:;" aria-label="Previous"><span aria-hidden="true"><i class="icon-arrow-left"></i></span></a></li>-->
                    <!--<li class="active"><a href="javascript:;">1 <span class="sr-only">(current)</span></a></li>-->
                    <!--<li><a href="javascript:;">2</a></li>-->
                    <!--<li><a href="javascript:;">3</a></li>-->
                    <!--<li><a href="javascript:;">4</a></li>-->
                    <!--<li><a href="javascript:;">...</a></li>-->
                    <!--<li><a href="javascript:;">100</a></li>-->
                    <!--<li><a href="javascript:;" aria-label="Next"><span aria-hidden="true"><i class="icon-arrow-right"></i></span></a></li>-->
                    <!--<li>-->
                    <!--<div class="input-group">-->
                    <!--<input type="text" class="form-control">-->
                    <!--<span class="input-group-btn"><a class="btn btn-primary">Go!</a></span>-->
                    <!--</div>-->
                    <!--</li>-->
                    <!--</ul>-->
                    <div id="example"></div>
                </div>
                <script type='text/javascript'>

                </script>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
</div>

<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
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
        //Enable iCheck plugin for checkboxes
        //iCheck for checkbox and radio inputs
        $('.mailbox-messages input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });

        //Enable check and uncheck all functionality
        $(".checkbox-toggle").click(function () {
            var clicks = $(this).data('clicks');
            if (clicks) {
                //Uncheck all checkboxes
                $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                $("i", this).removeClass("picon-check").addClass('picon-check-empty');
            } else {
                //Check all checkboxes
                $(".mailbox-messages input[type='checkbox']").iCheck("check");
                $("i", this).removeClass("picon-check-empty").addClass('picon-check');
            }
            $(this).data("clicks", !clicks);
        });
        // Tooltip
        //------------------------------------------
        $("[data-toggle='tooltip']").tooltip();
    });

    $(".btn-delete").bindClickChangeStateEvent("<?=base_url("/Admin/Service/delete")?>",-1,"删除",function(ele){
        ele.parent().parent().remove()
    });

    $("#search").click(function(){
        var ip = $("input[name='ip']").val();
        window.location.href = "<?=base_url("/Admin/Service/index")?>?ip="+ip;
    });

    $("#sentry_manage").addClass("active");
    $("#server_list_menu").addClass("active");
</script>
</body>
</html>
