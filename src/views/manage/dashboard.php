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
    <link rel="stylesheet" type="text/css" href="../static/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/nprogress.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/Chart.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/bootstrap-table.min.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/jquery.pjax.js"></script>
    <script src="../static/admin.function.js"></script>
    <script src="../static/popper.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
    <script src="../static/moment.min.js"></script>
    <script src="../static/nprogress.min.js"></script>
    <script src="../static/Chart.min.js"></script>
    <script src="../static/bootstrap-table.min.js"></script>
    <script>
        $.fn.extend({
            animateCss: function (animationName, callback) {
                var animationEnd = (function (el) {
                    var animations = {
                        animation: 'animationend',
                        OAnimation: 'oAnimationEnd',
                        MozAnimation: 'mozAnimationEnd',
                        WebkitAnimation: 'webkitAnimationEnd',
                    };

                    for (var t in animations) {
                        if (typeof (el.style[t]) !== 'undefined') {
                            return animations[t];
                        }
                    }
                })(document.createElement('div'));

                this.addClass('animated ' + animationName).one(animationEnd, function () {
                    $(this).removeClass('animated ' + animationName);
                    if (typeof callback === 'function') callback();
                });

                return this;
            },
        });
    </script>
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
                <li data-submenu="true">
                    <a class="menu-sideline"></a><i class="fa fa-external-link"></i><span>短链接</span>
                    <a class="treeview-toggle"><i class="fa fa-caret-left"></i></a>
                    <dl class="treeview">
                        <dd data-item="shortlink">普通短链接</dd>
                        <dd data-item="customlink">自定义短链接</dd>
                    </dl>
                </li>
                <li data-submenu="true">
                    <a class="menu-sideline"></a><i class="fa fa-user-o"></i><span>用户</span>
                    <a class="treeview-toggle"><i class="fa fa-caret-left"></i></a>
                    <dl class="treeview">
                        <dd data-item="user">用户管理</dd>
                        <dd data-item="userlog">登录日志</dd>
                    </dl>
                </li>
                <li data-submenu="true">
                    <a class="menu-sideline"></a><i class="fa fa-sitemap"></i><span>站点</span>
                    <a class="treeview-toggle"><i class="fa fa-caret-left"></i></a>
                    <dl class="treeview">
                        <dd data-item="site">站点管理</dd>
                        <dd data-item="accesslog">访问日志</dd>
                    </dl>
                </li>
                <li id="menu-item-logout">
                    <a class="menu-sideline"></a><i class="fa fa-power-off"></i><span>登出 (<?php echo $_SESSION['username']; ?>)</span>
                </li>
            </ul>
        </div>
    </aside>
    <div class="container main-container" id="main-container">
        <div class="container-header">
            <div class="container-header-content">
                <div class="model-title">
                    <span></span>
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
                <div class="menu-toggle">
                    <button class="btn btn-primary" type="button" onclick="showMenu();">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="container-right" id="container-right">
            <?php
            if (isset($_GET['p'])){
                switch($_GET['p']){
                    case 'user':case 'userlog':case 'shortlink':case 'customlink':case 'site':case 'data':case 'accesslog':
                        include_once('./right/right_'.$_GET['p'].'.html');
                        break;
                    default:
                        include_once('./right/right_error.html');
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
                    if ($(this).children('.treeview-toggle').children('i').hasClass('fa-caret-left')){
                        //未展开
                        $(this).children('.treeview-toggle').children('i').removeClass('fa-caret-left');
                        $(this).children('.treeview-toggle').children('i').addClass('fa-caret-down');
                        $(this).children('.treeview').addClass('treeview-active');
                        $(this).children('.treeview').animateCss('fadeIn faster');
                        $(this).addClass('menu-item-expanded');
                        $('[data-submenu=true][data-subitem-selected!=true]').removeClass('menu-item-selected');
                        $(this).addClass('menu-item-selected');
                    } else {
                        //已展开
                        $(this).children('.treeview-toggle').children('i').removeClass('fa-caret-down');
                        $(this).children('.treeview-toggle').children('i').addClass('fa-caret-left');
                        $(this).children('.treeview').removeClass('treeview-active');
                        $(this).removeClass('menu-item-expanded');
                        $(this).removeClass('menu-item-selected');
                    }
                } else {
                    $('.menu-item-selected').removeClass('menu-item-selected');
                    $('.menu-subitem-selected').removeClass('menu-subitem-selected');
                    $('.treeview-item-selected').removeClass('treeview-item-selected');
                    $(this).addClass('menu-item-selected');
                        if (screen.width<=1024){
                        if (menu_showed){
                            menu_showed = false;
                            $('.container-menu').animateCss('fadeOut faster', function(){
                                $('.container-menu').hide();
                            });
                        }
                    }
                }
            });

            var page = getUrlParam('p');
            switch(page){
                case 'data':
                    $('[data-item='+page+']').addClass('menu-item-selected');
                    break;
                default:
                    $('[data-item='+page+']').addClass('treeview-item-selected')
                    $('[data-item='+page+']').parent().addClass('treeview-active');
                    $('[data-item='+page+']').parent().parent().addClass('menu-item-selected');
                    $('[data-item='+page+']').parent().parent().addClass('menu-item-expanded');
                    $('[data-item='+page+']').parent().parent().addClass('menu-subitem-selected');
                    $('[data-item='+page+']').parent().parent().children('.treeview-toggle').children('i').removeClass('fa-caret-left');
                    $('[data-item='+page+']').parent().parent().children('.treeview-toggle').children('i').addClass('fa-caret-down');
                    break;
            }

            $('.user-loginout').click(function () {
               logout();
            });

            function logout(){
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
                            var c = getCookie('autologin');
                            if (c != null){
                                c = c.split('|');
                                setCookie('autologin','',-1);
                                setCookie_seconds('autologin_checked','true',300);
                                setCookie_seconds('autologin_username',c[0],300);
                            }
                            window.location.href="/manage/";
                        } else {
                            toastr.error(data.error);
                            setTimeout(function(){
                                window.location.href="/manage/";
                            },200);
                        }
                    },
                    error: function (err) {
                        console.error(err);
                    }
                });
            }

            $('dd').click(function(e){
                $('.treeview-item-selected').removeClass('treeview-item-selected');
                $(this).addClass('treeview-item-selected');
                $('.menu-item-selected').removeClass('menu-item-selected');
                $(this).parent().parent().addClass('menu-item-selected');
                $('.menu-subitem-selected').removeClass('menu-subitem-selected');
                $(this).parent().parent().addClass('menu-subitem-selected');
                $('[data-subitem-selected]').removeAttr('data-subitem-selected');
                $(this).parent().parent().attr('data-subitem-selected','true');
                if (screen.width<=1024){
                    if (menu_showed){
                        menu_showed = false;
                        $('.container-menu').animateCss('fadeOut faster', function(){
                            $('.container-menu').hide();
                        });
                    }
                }
                e.stopPropagation();
            });

            $('[data-item]').click(function(){
                $.pjax({
                    url:'/manage/?p='+$(this).attr('data-item'),
                    container:'#container-right',
                    fragment: '#container-right'
                });
            });

            $(document).on('pjax:start', function() { NProgress.start(); });
            $(document).on('pjax:end',   function() {
                NProgress.done();
                var nav = document.getElementById('navbarResponsive');
                if (nav != undefined){
                    nav.setAttribute('class','navbar-collapse collapse');
                }
            });

            $(document).on('click','.tablebtn-del',function(){
                if (typeof $(this).attr('data-delconfirm') == "undefined" || $(this).attr('data-delconfirm')!="true"){
                    let t = $(this);
                    t.append('<span style="margin-left:8px;">确认删除？</span>');
                    t.attr('data-delconfirm','true');
                    setTimeout(function(){
                        t.removeAttr('data-delconfirm');
                        t.children('span').remove();
                    },3000);
                }
            });

            var menu_showed = false;
            function showMenu(){
                if (menu_showed){
                    $('.container-menu').animateCss('fadeOut faster', function(){
                        $('.container-menu').hide();
                        menu_showed = false;
                    });
                } else {
                    menu_showed = true;
                    $('.container-menu').show();
                    $('.container-menu').animateCss('fadeIn faster');
                }
            }

            $('#menu-item-logout').click(function(){
                logout();
            })
        </script>
    </div>
</body>