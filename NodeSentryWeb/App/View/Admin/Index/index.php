<?php
    include_once __VIEW__ . "Admin/css_header.php";
?>

<body class="hold-transition skin-blue sidebar-mini">
<!-- Main Header -->
<div id="header">
    <?php
        include_once __VIEW__ . "Admin/header.php";
    ?>
</div>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <div id="left">
        <?php
            include_once __VIEW__ . "Admin/left.php";
        ?>
    </div>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper scroll_bar">
        <div class="content">
            <ol class="breadcrumb">
                <li><a href="javascript:;"><i class="icon-gear"></i> 统计</a></li>
                <li class="active">展示面板</li>
            </ol>
            <div class="row">
                <!--<div class="col-lg-3 col-xs-12">-->
                <!--&lt;!&ndash; small box &ndash;&gt;-->
                <!--<div class="small-box bg-aqua">-->
                <!--<div class="inner">-->
                <!--<h3>15005400</h3>-->
                <!--<p>访客量</p>-->
                <!--</div>-->
                <!--<div class="icon"><i class="icon-store"></i></div>-->
                <!--</div>-->
                <!--</div>-->
                <!-- ./col -->
                <div class="col-lg-4 col-xs-12">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3><?=$week_count?></h3>
                            <p>本周日志总数</p>
                        </div>
                        <div class="icon"><i class="icon-info"></i></div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-xs-12">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3><?=$month_count?></h3>
                            <p>本月日志总数</p>
                        </div>
                        <div class="icon"><i class="icon-info"></i></div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-xs-12">
                    <!-- small box -->
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3><?=$all_count?></h3>
                            <p>总日志数</p>
                        </div>
                        <div class="icon"><i class="icon-info"></i></div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div id="main_statistic" class="statistic" style="height:400px;"></div>

            <?php if($ip_list){ ?>
            <div class="row">
                <div class="col-lg-12">
                    <!-- list -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h4 class="box-title">监控机器排行</h4>
                            <div class="box-number pull-right">日志总数</div>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <ul class="list-unstyled products-list product-list-in-box">

                                <?php foreach ($ip_list as $key=>$client){ ?>

                                    <li class="item">
                                    <div class="product-img"><img src="<?=site_url(__PUBLIC__."img/temp/server.jpg")?>" alt="Product Image"></div>
                                    <div class="product-info">
                                        <a href="javascript:void(0)" class="product-title"><span class="label label-warning pull-right"><?=$client["log_count"]?></span></a>
                                        <span class="product-description"><?=$client["client_ip"]?></span>
                                    </div>
                                </li>

                                <?php } ?>

                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-center">
                            <a href="javascript:void(0)" class="uppercase">更多 <i class="icon-arrow-right"></i></a>
                        </div>
                        <!-- /.box-footer -->
                    </div>

                </div>

            </div>
            <?php }else{ ?>
            <div class="empty text-center">
                <dl>
                    <dt><i class="icon-empty"></i></dt>
                    <dd>暂时没有监控设备！</dd>
                </dl>
            </div>
            <?php } ?>
        </div>
    </div>
    <!-- /.content-wrapper -->
</div>

<?php include_once __VIEW__ . "Admin/js_loader.php" ?>
<!-- echarts -->
<script src="<?=site_url(__PUBLIC__."js/plugins/echarts/echarts.common.min.js")?>"></script>
<script type="text/javascript">
    // scrollbar 滚动条
    //------------------------------------------
    jQuery(document).ready(function ($) {
        "use strict";
        $('.scroll_bar').perfectScrollbar();
    });
    // ECharts.js
    //------------------------------------------
    // 基于准备好的dom，初始化echarts实例
    var statistic = echarts.init(document.getElementById('main_statistic'));

    // 指定图表的配置项和数据
    var option = {
//        title: {
//            text: '统计详情'
//        },
        backgroundColor: '#fff',
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:['课程总数','总交易量','总用户量']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: ['周一','周二','周三','周四','周五','周六','周日']
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name:'课程总数',
                type:'line',
                stack: '总量',
                data:[120, 132, 101, 134, 90, 230, 210]
            },
            {
                name:'总交易量',
                type:'line',
                stack: '总量',
                data:[220, 182, 191, 234, 290, 330, 310]
            },
            {
                name:'总用户量',
                type:'line',
                stack: '总量',
                data:[150, 232, 201, 154, 190, 330, 410]
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    statistic.setOption(option);

    $("#index_menu").addClass("active");
</script>

</body>
</html>
