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
                        <span>无法连接数据库，站点暂停服务。</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>