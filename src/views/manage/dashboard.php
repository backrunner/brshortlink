<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?php echo SITE_NAME; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../static/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/admin.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/toastr.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/awesome-bootstrap-checkbox.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
</head>
<body>
    <div class="container main-container" id="main-container">
        <aside class="container-menu">
            <div class="menu-header">
                <div class="menu-title">
                    <span>短链接管理后台</span>
                </div>
            </div>
            <div class="menu-body">
                <ul class="menu" id="dashboard-menu">
                    <li><i class="fa fa-database"></i><span>数据</span></li>
                    <li><i class="fa fa-external-link"></i><span>短链接</span></li>
                    <li><i class="fa fa-user-o"></i><span>用户</span></li>
                    <li><i class="fa fa-sitemap"></i><span>站点</span></li>
                </ul>
            </div>
        </aside>
        <div class="container-header">
            <div class="container-header-content">
                <div class="model-title">
                    <span>[Title]</span>
                </div>
                <div class="user">
                    <div class="user-name">
                        <span>[Username]</span>
                    </div>
                    <div class="user-loginout">
                        <i class="fa fa-power-off"></i>
                        <span>退出登录</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-right container-data" id="container-data">

        </div>
    </div>
</body>