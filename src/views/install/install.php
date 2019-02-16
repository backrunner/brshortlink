<?php if (isset($_POST['action']) && !file_exists('../install.lock')) {
    //通过ajax执行动作
    if ($_POST['action'] == 'install' && isset($_POST['sitename']) && isset($_POST['dbhost'])
        && isset($_POST['dbport']) && isset($_POST['dbname']) &&  isset($_POST['dbusername'])
        && isset($_POST['dbpassword']) && isset($_POST['mgusername']) && isset($_POST['mgpassword'])){
        //安装
        $mysqli = @new mysqli($_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], $_POST['dbname'],$_POST['dbport']);
        if ($mysqli->connect_errno){
            echo json_encode(array('type'=>'error','msg'=>'连接数据库错误：'.$mysqli->connect_error));
            return;
        }
        $mysqli->query("set names 'utf8';");
        $create_query = "CREATE TABLE `shortlinks` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `url` varchar(4096) NOT NULL,
            `urlhash` varchar(40) NOT NULL,
            `ctime` int(11) unsigned NOT NULL,
            `expires` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX (`urlhash`)
        ) ENGINE=MyISAM AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8";
        $create_query_log = "CREATE TABLE `shortlinks_log` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `linkid` int(11) unsigned NOT NULL,
            `count` bigint(20) unsigned NOT NULL,
            `lasttime` int(11) unsigned DEFAULT NULL,
            PRIMARY KEY (`linkid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $create_query_custom = "CREATE TABLE `shortlinks_custom` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `cname` varchar(64) NOT NULL UNIQUE,
            `url` varchar(4096) NOT NULL,
            `ctime` int(11) unsigned NOT NULL,
            `expires` int(11) DEFAULT NULL,
            PRIMARY KEY (`cname`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $create_query_user = "CREATE TABLE `managers` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `username` varchar(32) NOT NULL UNIQUE,
            `password` varchar(64) NOT NULL,
            `ctime` int(11) unsigned NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $create_query_userlog = "CREATE TABLE `managers_log` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `userid` int(11) unsigned NOT NULL,
            `logintime` int(11) unsigned NOT NULL,
            `loginip` varchar(20) DEFAULT NULL,
            INDEX (`userid`),
            INDEX (`logintime`),
            INDEX (`loginip`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $create_query_manager = 'INSERT INTO `managers` (username, password, ctime) VALUES ("'.$_POST['mgusername'].'","'.hash('sha256', $_POST['mgpassword']).'", '.time().')';
        $res_query = $mysqli->query($create_query);
        if (!$res_query){
            echo json_encode(array('type'=>'error','msg'=>'创建数据表时发生错误:'.$res_query->error));
            return;
        }
        $res_query_custom = $mysqli->query($create_query_custom);
        if (!$res_query_custom){
            echo json_encode(array('type'=>'error','msg'=>'创建数据表时发生错误:'.$res_query_custom->error));
            return;
        }
        $res_query_log = $mysqli->query($create_query_log);
        if (!$res_query_log){
            echo json_encode(array('type'=>'error','msg'=>'创建日志表时发生错误:'.$res_query_log->error));
            return;
        }
        $res_query_user = $mysqli->query($create_query_user);
        if (!$res_query_user){
            echo json_encode(array('type'=>'error','msg'=>'创建用户表时发生错误:'.$res_query_user->error));
            return;
        }
        $res_query_userlog = $mysqli->query($create_query_userlog);
        if (!$res_query_userlog){
            echo json_encode(array('type'=>'error','msg'=>'创建用户日志表时发生错误:'.$res_query_userlog->error));
            return;
        }
        $res_query_manager = $mysqli->query($create_query_manager);
        if (!$res_query_manager){
            echo json_encode(array('type'=>'error','msg'=>'创建管理员时发生错误:'.$res_query_manager->error));
            return;
        }
        $mysqli->close();
        try{
            $config = str_replace("\t","","<?php
            //基本配置
            define('SITE_NAME','".str_replace("'","\'",$_POST['sitename'])."');
            //数据库配置
            define('DB_HOST','".$_POST['dbhost']."');
            define('DB_PORT','".$_POST['dbport']."');
            define('DB_USER','".$_POST['dbusername']."');
            define('DB_PASSWORD','".$_POST['dbpassword']."');
            define('DB_NAME', '".$_POST['dbname']."');
            ?>");
            file_put_contents('../config.php', $config, LOCK_EX);
            file_put_contents('../install.lock','');
        } catch (Exception $e){
            echo json_encode(array('type'=>'error','msg'=>'写入配置文件时出现错误:'.$e->getMessage()));
            return;
        }
        echo json_encode(array('type'=>'success','msg'=>'安装完成，正在跳转至主页。'));
    } else {
        echo json_encode(array('type'=>'error','msg'=>'提交的参数错误。'));
    }
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
                            <div class="col-sm-10"><input type="text" id="i-dbusername" class="form-control"  value="<?php echo defined('DB_USER')?DB_USER:'root'?>"></div>
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
            function submitInstall(){
                $('.btn').attr('disabled','disabled');
                $('.input').attr('disabled','disabled');
                $.ajax({
                    url: '/install/install.php',
                    type:'POST',
                    data:{
                        action: 'install',
                        sitename: $('#i-sitename').val(),
                        dbhost: $('#i-dbhost').val(),
                        dbport: $('#i-dbport').val(),
                        dbname: $('#i-dbname').val(),
                        dbusername: $('#i-dbusername').val(),
                        dbpassword: $('#i-dbpassword').val(),
                        mgusername: $('#i-mgusername').val(),
                        mgpassword: CryptoJS.SHA256($('#i-mgpassword').val()).toString(),
                    },
                    dataType: 'json',
                    success: function(data){
                        console.log(data);
                        $('.btn').removeAttr('disabled');
                        $('.input').removeAttr('disabled');
                        if (data.type == 'success'){
                            toastr.success(data.msg);
                            setTimeout(function(){
                                window.location.href="/";
                            },2500);
                        } else {
                            toastr.error(data.msg);
                        }
                    },
                    error: function(data){
                        console.log(data);
                        $('.btn').removeAttr('disabled');
                        $('.input').removeAttr('disabled');
                        toastr.error(htmlEncode(data.responseText));
                    }
                });
            }

            function htmlEncode(s){
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(s));
                return div.innerHTML;
            }
        </script>
    </body>
    </html>
<?php }?>