<?php if (isset($_POST['action'])) {
    //通过ajax执行动作
} else {
    //非执行动作时渲染页面
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>安装 - BackRunner's ShortLink</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="../static/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../static/main.min.css" />
        <script src="../static/jquery.min.js"></script>
        <script src="../static/bootstrap.min.js"></script>
    </head>
    <body class="install-body">
        <div class="container install-container" id="main-container">
            <div class="row">
                <div class="col-lg-12 page-header">
                    <h2>B<mhide>ack</mhide>R<mhide>unner</mhide>'s ShortLink</h2>
                    <label class="page-header-label">安装</label>
                </div>
            </div>
            <?php if (file_exists('../install.lock')) {?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <span>错误</span>
                        </div>
                        <div class="card-body">
                            <span>请勿重复安装。</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php } else {
                    //config.php 存在
                    $isConfigExisted = false;
                    if (file_exists('../config.php')){
                        $isConfigExisted = true;
                        include_once('../config.php');
                    }
                ?>
            <div class="row">
                <div class="col-lg-12 header">
                    <h3>数据库配置</h3>
                </div>
                <?php if ($isConfigExisted){?>
                <div class="col-lg-12">
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        检测到存在的配置文件，已导入配置。
                    </div>
                </div>
                <?php }?>
                <div class="col-lg-12">
                    <form>
                        <div class="form-row">
                            <div class="col-sm-2"><label>地址</label></div>
                            <div class="col-sm-10"><input type="text" class="form-control" value="<?php echo defined('DB_HOST')?DB_HOST:'localhost'?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-2"><label>端口</label></div>
                            <div class="col-sm-10"><input type="number" max="65535" class="form-control" value="<?php echo defined('DB_PORT')?DB_PORT:'3306'?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-2"><label>数据库名称</label></div>
                            <div class="col-sm-10"><input type="text" class="form-control" value="<?php echo defined('DB_NAME')?DB_NAME:''?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-2"><label>数据库用户名</label></div>
                            <div class="col-sm-10"><input type="text" class="form-control"  value="<?php echo defined('DB_USERNAME')?DB_USERNAME:''?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-2"><label>数据库密码</label></div>
                            <div class="col-sm-10"><input type="password" class="form-control" value="<?php echo defined('DB_PASSWORD')?DB_PASSWORD:'3306'?>"></div>
                        </div>
                        <mhide>
                        <div class="form-row">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2"><button class="btn btn-block btn-primary" style="float:right;">安装</button></div>
                        </div>
                        </mhide>
                        <mvisi>
                        <div class="form-row">
                            <div class="col-sm-2"><label>安装</label></div>
                            <div class="col-sm-10"><button class="btn btn-block btn-primary" style="float:right;">安装</button></div>
                        </div>
                        </mvisi>
                    </form>
                </div>
            </div>
            <?php }?>
        </div>
    </body>
    </html>

<?php }?>