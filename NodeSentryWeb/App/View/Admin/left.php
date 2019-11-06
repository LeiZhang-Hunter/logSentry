<aside class="main-sidebar">
    <!-- sidebar: sidebar.less -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?=site_url("/Public/img/temp/user2-160x160.jpg")?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Leon.Zhu</p>
                <a href="javascript:;"><i class="icon-circle"></i> 超级管理员</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="搜索">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="icon-magnifier"></i></button>
                    </span>
            </div>
        </form>
        <!-- sidebar menu: : sidebar.less -->
        <div class="scroll_bar">
            <ul class="sidebar-menu">

                <li id="index_menu" class="treeview">
                    <a href="/Admin/Index/index">
                        <i class="icon-gear"></i> <span>首页</span> <i class="icon-arrow-left pull-right"></i>
                    </a>
                </li>

                <li id="system_config" class="treeview">
                    <a href="javascript:;">
                        <i class="icon-gear"></i> <span>系统设置</span> <i class="icon-arrow-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">


                        <li id="user_list_menu">
                            <a href="<?=base_url("/Admin/User/index")?>"><i class="icon-circle"></i> 用户列表 </a>
                        </li>

                        <li id="dingding_config_menu">
                            <a href="<?=base_url("/Admin/DingDing/index")?>"><i class="icon-circle"></i> 钉钉配置 </a>
                        </li>
                    </ul>
                </li>

                <li id="sentry_manage" class="treeview">
                    <a href="javascript:;">
                        <i class="icon-gear"></i> <span>日志系统</span> <i class="icon-arrow-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="server_list_menu">
                            <a href="<?=base_url("/Admin/Service/index")?>"><i class="icon-circle"></i> 服务中心 <i class="icon-arrow-left pull-right"></i></a>
                        </li>
<!--                        <li id="project_list_menu">-->
<!--                            <a href="--><?//=base_url("/Admin/Project/index")?><!--"><i class="icon-circle"></i> 项目列表 <i class="icon-arrow-left pull-right"></i></a>-->
<!--                        </li>-->
<!--                        <li id="log_list_menu">-->
<!--                            <a href="--><?//=base_url("/Admin/LogList/logCollect")?><!--"><i class="icon-circle"></i> 日志列表 <i class="icon-arrow-left pull-right"></i></a>-->
<!--                        </li>-->
                    </ul>
                </li>

                <li id="debug_manage" class="treeview">
                    <a href="javascript:;">
                        <i class="icon-gear"></i> <span>调试中心</span> <i class="icon-arrow-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="debug_explain">
                            <a href="<?=base_url("/Admin/Debug/explain")?>"><i class="icon-circle"></i> 安装使用说明</a>
                        </li>
                        <li id="debug_download">
                            <a href="<?=base_url("/Admin/Debug/downloadPage")?>"><i class="icon-circle"></i> 谷歌插件下载</a>
                        </li>
                        <li id="debug_config">
                            <a href="<?=base_url("/Admin/LogList/logCollect")?>"><i class="icon-circle"></i> 插件配置</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
    <!-- /.sidebar -->
</aside>