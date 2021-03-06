<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>安装 - BackRunner's ShortLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../static/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/main.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/toastr.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/awesome-bootstrap-checkbox.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
    <script src="../static/crypto-js.js"></script>
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
            <?php if ($isConfigExisted){?>
            <div class="col-lg-12">
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    检测到存在的配置文件，已导入配置。
                </div>
            </div>
            <?php }?>
        </div>
        <?php if (!extension_loaded('bcmath')){?>
        <div class="row">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                警告：未检测到可用的bcmath扩展，站点将不可用！
            </div>
        </div>
        <?php }?>
        <div class="row">
            <div class="col-lg-12 header">
                <h3>站点信息配置</h3>
            </div>
            <div class="col-lg-12">
                <form>
                    <div class="form-row">
                        <div class="col-sm-2"><label>站点名称</label></div>
                        <div class="col-sm-10"><input type="text" id="i-sitename" class="form-control" value="<?php echo defined('SITE_NAME')?SITE_NAME:'BackRunner\'s ShortLink'?>"></div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 header">
                <h3>管理员配置</h3>
            </div>
            <div class="col-lg-12">
                <div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>管理员用户名</label></div>
                        <div class="col-sm-10"><input type="text" id="i-mgusername" class="form-control"></div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>管理员密码</label></div>
                        <div class="col-sm-10"><input type="password" id="i-mgpassword" class="form-control"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 header">
                <h3>数据库配置</h3>
            </div>
            <div class="col-lg-12">
                <div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>地址</label></div>
                        <div class="col-sm-10"><input type="text" id="i-dbhost" class="form-control" value="<?php echo defined('DB_HOST')?DB_HOST:'localhost'?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>端口</label></div>
                        <div class="col-sm-10"><input type="number" id="i-dbport" max="65535" class="form-control" value="<?php echo defined('DB_PORT')?DB_PORT:'3306'?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>数据库名称</label></div>
                        <div class="col-sm-10"><input type="text" id="i-dbname" class="form-control" value="<?php echo defined('DB_NAME')?DB_NAME:'brshortlink'?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>数据库用户名</label></div>
                        <div class="col-sm-10"><input type="text" id="i-dbusername" class="form-control" value="<?php echo defined('DB_USER')?DB_USER:'root'?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>数据库密码</label></div>
                        <div class="col-sm-10"><input type="password" id="i-dbpassword" class="form-control" value="<?php echo defined('DB_PASSWORD')?DB_PASSWORD:''?>"></div>
                    </div>
                    <mhide>
                        <div class="form-row">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2"><button class="btn btn-block btn-primary" id="btn-submitinstall" onclick="submitInstall();">安装</button></div>
                        </div>
                    </mhide>
                    <mvisi>
                        <div class="form-row">
                            <div class="col-sm-2"><label>安装</label></div>
                            <div class="col-sm-10"><button class="btn btn-block btn-primary" id="btn-submitinstall-mobile" onclick="submitInstall();">安装</button></div>
                        </div>
                    </mvisi>
                </div>
            </div>
        </div>
        <?php }?>
    </div>
    <script>
        function submitInstall() {
            //表单检查
            var sitename = $('#i-sitename').val();
            var dbhost = $('#i-dbhost').val();
            var dbport = $('#i-dbport').val();
            var dbname = $('#i-dbname').val();
            var dbusername = $('#i-dbusername').val();
            var dbpassword = $('#i-dbpassword').val();
            var mgusername = $('#i-mgusername').val();
            var mgpassword = $('#i-mgpassword').val();
            let flag_check = false;
            if (sitename.length < 1) {
                $('#i-sitename').addClass('is-invalid');
                flag_check = true;
            }
            if (dbhost.length < 1) {
                $('#i-dbhost').addClass('is-invalid');
                flag_check = true;
            }
            if (dbport.length < 1) {
                $('#i-dbport').addClass('is-invalid');
                flag_check = true;
            }
            if (dbname.length < 1) {
                $('#i-dbname').addClass('is-invalid');
                flag_check = true;
            }
            if (dbusername.length < 1) {
                $('#i-dbusername').addClass('is-invalid');
                flag_check = true;
            }
            if (dbpassword.length < 1) {
                $('#i-dbpassword').addClass('is-invalid');
                flag_check = true;
            }
            if (mgusername.length < 1) {
                $('#i-mgusername').addClass('is-invalid');
                flag_check = true;
            }
            if (mgpassword.length < 1) {
                $('#i-mgpassword').addClass('is-invalid');
                flag_check = true;
            }
            if (flag_check) {
                toastr.error('有表单项未填写，请检查表单。');
                return;
            }
            $('.btn').attr('disabled', 'disabled');
            $('.btn').html('安装中...');
            $('.input').attr('disabled', 'disabled');
            $.ajax({
                url: '/install/install.php',
                type: 'POST',
                data: {
                    action: 'install',
                    sitename: sitename,
                    dbhost: dbhost,
                    dbport: dbport,
                    dbname: dbname,
                    dbusername: dbusername,
                    dbpassword: dbpassword,
                    mgusername: mgusername,
                    mgpassword: CryptoJS.SHA256(mgpassword).toString()
                },
                dataType: 'json',
                success: function (data) {
                    if (data.type == 'success') {
                        toastr.success(data.msg);
                        $('.btn').html('安装成功');
                        setTimeout(function () {
                            window.location.href = "/";
                        }, 2500);
                    } else {
                        toastr.error(data.msg);
                        $('.btn').removeAttr('disabled');
                        $('.input').removeAttr('disabled');
                        $('.btn').html('安装');
                    }
                },
                error: function (data) {
                    console.error(data);
                    $('.btn').removeAttr('disabled');
                    $('.input').removeAttr('disabled');
                    $('.btn').html('安装');
                    toastr.error(htmlEncode(data.responseText));
                }
            });
        }

        function htmlEncode(s) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(s));
            return div.innerHTML;
        }
    </script>

    <?php if (!is_mobile_request()){ ?>
    <script color="28,28,28" opacity='0.7' zIndex="-2" count="120" src="../static/canvas-nest.js"></script>
    <?php } ?>
</body>

</html>