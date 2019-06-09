<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        短链接管理 - <?php echo SITE_NAME; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../static/bootswatch-materia.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/admin.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/toastr.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/awesome-bootstrap-checkbox.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
</head>

<body>
    <aside class="container-menu">
        <div class="menu-header">
            <div class="menu-title">
                <span>短链接管理后台</span>
            </div>
        </div>
        <div class="menu-body">
            <ul class="menu" id="dashboard-menu">
                <li data-item="data">
                    <a class="menu-sideline"></a><i class="fa fa-database"></i><span>数据</span>
                </li>
                <li data-item="shortlink" data-submenu="true">
                    <a class="menu-sideline"></a><i class="fa fa-external-link"></i><span>短链接</span>
                    <a class="treeview-toggle"><i class="fa fa-caret-left"></i></a>
                    <dl class="treeview">
                        <dd>短链接管理</dd>
                        <dd>访问日志</dd>
                    </dl>
                </li>
                <li data-item="user" data-submenu="true">
                    <a class="menu-sideline"></a><i class="fa fa-user-o"></i><span>用户</span>
                    <a class="treeview-toggle"><i class="fa fa-caret-left"></i></a>
                    <dl class="treeview">
                        <dd>用户管理</dd>
                        <dd>登录日志</dd>
                    </dl>
                </li>
                <li data-item="sitemap">
                    <a class="menu-sideline"></a><i class="fa fa-sitemap"></i><span>站点</span>
                </li>
            </ul>
        </div>
    </aside>
    <div class="container main-container" id="main-container">
        <div class="container-header">
            <div class="container-header-content">
                <div class="model-title">
                    <span>数据</span>
                </div>
                <div class="user">
                    <div class="user-name">
                        <span><?php echo $_SESSION['username']; ?></span>
                    </div>
                    <div class="user-loginout">
                        <i class="fa fa-power-off"></i>
                        <span>退出登录</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-right">
            <?php
            if (isset($_GET['p'])){
                switch($_GET['p']){
                    case 'data':
                    include_once('./right/right_data.html');
                    break;
                    case 'user':
                    include_once('./right/right_user.html');
                    break;
                }
            } else {
                include_once('./right/right_error.html');
            }
            ?>
        </div>
        <script>
            //初始化
            $('.menu li').click(function () {
                if ($(this).children('.treeview-toggle').length > 0){
                    $('[data-submenu=true]').removeClass('menu-item-selected');
                    if ($(this).children('.treeview-toggle').children('i').hasClass('fa-caret-left')){
                        $(this).children('.treeview-toggle').children('i').removeClass('fa-caret-left');
                        $(this).children('.treeview-toggle').children('i').addClass('fa-caret-down');
                        $(this).children('.treeview').addClass('treeview-active');
                        $(this).addClass('menu-item-expanded');
                        $(this).addClass('menu-item-selected');
                    } else {
                        $(this).children('.treeview-toggle').children('i').removeClass('fa-caret-down');
                        $(this).children('.treeview-toggle').children('i').addClass('fa-caret-left');
                        $(this).children('.treeview').removeClass('treeview-active');
                        $(this).removeClass('menu-item-expanded');
                        $(this).removeClass('menu-item-selected');
                    }
                } else {
                    $('.menu-item-selected').removeClass('menu-item-selected');
                    $('.treeview-item-selected').removeClass('treeview-item-selected');
                    $(this).addClass('menu-item-selected');
                }
            });

            $('[data-item=data]').addClass('menu-item-selected');

            $('.user-loginout').click(function () {
                $.ajax({
                    type: 'POST',
                    url: 'action.php',
                    data: {
                        action: 'logout',
                        username: '<?php echo $_SESSION['username']; ?>'
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.code == 200) {
                            window.location.reload();
                        } else {
                            toastr.error(data.error);
                        }
                    },
                    error: function (err) {
                        console.error(err);
                    }
                });
            });

            $('dd').click(function(e){
                $('.treeview-item-selected').removeClass('treeview-item-selected');
                $(this).addClass('treeview-item-selected');
                $('.menu-item-selected').removeClass('menu-item-selected');
                $(this).parent().parent().addClass('menu-item-selected');
                e.stopPropagation();
            });
        </script>
    </div>
</body>