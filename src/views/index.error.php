<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo SITE_NAME;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../static/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/main.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/toastr.min.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
    <script src="../static/crypto-js.js"></script>
</head>

<body class="install-body">
    <div class="container install-container" id="main-container">
        <div class="row">
            <div class="col-lg-12 page-header">
                <h2><?php displayTitle();?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <span>错误</span>
                    </div>
                    <div class="card-body">
                        <span><?php
                            switch($error_code){
                                case 100:
                                echo '无法连接到数据库，站点暂停使用。';
                                break;
                                case 300:
                                echo '查询短链接时出现错误，请重试。';
                                break;
                                case 301:
                                echo '查询自定义短链接时出现错误，请重试。';
                                break;
                                case 303:
                                echo '该链接已过期，无法访问。';
                                break;
                                case 304:
                                echo '找不到对应的短链接。';
                                break;
                            }
                        ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>