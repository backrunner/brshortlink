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
        $mysqli->query("set names 'utf8'");
        //启动事务
        $res_event = $mysqli->query("begin");
        if (!$res_event){
            echo json_encode(array('type'=>'error','msg'=>'启动事务时发生错误：'.mysqli_error($mysqli)));
            return;
        }
        $create_query = "CREATE TABLE `shortlinks` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `url` varchar(4096) NOT NULL,
            `urlhash` varchar(40) NOT NULL,
            `ctime` int(11) unsigned NOT NULL,
            `expires` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX (`urlhash`)
        ) ENGINE=MyISAM AUTO_INCREMENT=".rand(10000,50000)." DEFAULT CHARSET=utf8";
        $create_query_log = "CREATE TABLE `shortlinks_log` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `linkid` int(11) unsigned NOT NULL,
            `count` bigint(20) unsigned NOT NULL,
            `lasttime` int(11) unsigned DEFAULT NULL,
            PRIMARY KEY (`linkid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $create_query_logdetail = "CREATE TABLE `shortlinks_logdetail` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `type` int(1) NOT NULL,
            `linkid` int(11) unsigned NOT NULL,
            `count` bigint(20) unsigned NOT NULL,
            `time` int(11) unsigned NOT NULL,
            `ip` varchar(50) DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX (`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $create_query_custom = "CREATE TABLE `shortlinks_custom` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `cname` varchar(64) NOT NULL UNIQUE,
            `url` varchar(4096) NOT NULL,
            `ctime` int(11) unsigned NOT NULL,
            `expires` int(11) DEFAULT NULL,
            PRIMARY KEY (`cname`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $create_query_custom_log = "CREATE TABLE `shortlinks_custom_log` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `linkid` int(11) unsigned NOT NULL,
            `count` bigint(20) unsigned NOT NULL,
            `lasttime` int(11) unsigned DEFAULT NULL,
            PRIMARY KEY (`linkid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
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
            `loginip` varchar(50) DEFAULT NULL,
            INDEX (`userid`),
            INDEX (`logintime`),
            INDEX (`loginip`),
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $create_query_access = "CREATE TABLE `access` (
            `type` varchar(4) NOT NULL UNIQUE,
            `count` int(11) unsigned NOT NULL,
            PRIMARY KEY (`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $create_query_accesslog = "CREATE TABLE `access_log` (
            `id` int(11) unsigned NOT NULL UNIQUE AUTO_INCREMENT,
            `time` int(11) unsigned NOT NULL,
            `ip` varchar(50) DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX (`ip`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $create_query_manager = 'INSERT INTO `managers` (username, password, ctime) VALUES ("'.$_POST['mgusername'].'","'.hash('sha256', $_POST['mgpassword']).'", '.time().')';
        $create_query_accesstype = 'INSERT INTO access (type, count) VALUES ("uv","0");INSERT INTO access (type, count) VALUES ("pv","0");';
        $res_query = $mysqli->query($create_query);
        if (!$res_query){
            echo json_encode(array('type'=>'error','msg'=>"创建数据表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_custom = $mysqli->query($create_query_custom);
        if (!$res_query_custom){
            echo json_encode(array('type'=>'error','msg'=>"创建数据表时发生错误\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_log = $mysqli->query($create_query_log);
        if (!$res_query_log){
            echo json_encode(array('type'=>'error','msg'=>"创建日志表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_logdetail = $mysqli->query($create_query_logdetail);
        if (!$res_query_logdetail){
            echo json_encode(array('type'=>'error','msg'=>"创建短链接日志时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_custom_log = $mysqli->query($create_query_custom_log);
        if (!$res_query_custom_log){
            echo json_encode(array('type'=>'error','msg'=>"创建自定义链接日志表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_user = $mysqli->query($create_query_user);
        if (!$res_query_user){
            echo json_encode(array('type'=>'error','msg'=>"创建用户表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_userlog = $mysqli->query($create_query_userlog);
        if (!$res_query_userlog){
            echo json_encode(array('type'=>'error','msg'=>"创建用户日志表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_access = $mysqli->query($create_query_access);
        if (!$res_query_access){
            echo json_encode(array('type'=>'error','msg'=>"创建访问记录表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_accesslog = $mysqli->query($create_query_accesslog);
        if (!$res_query_accesslog){
            echo json_encode(array('type'=>'error','msg'=>"创建访问记录表时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_manager = $mysqli->query($create_query_manager);
        if (!$res_query_manager){
            echo json_encode(array('type'=>'error','msg'=>"创建管理员时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        $res_query_accesstype = $mysqli->multi_query($create_query_accesstype);
        if (!$res_query_manager){
            echo json_encode(array('type'=>'error','msg'=>"创建访问记录类型时发生错误:\n".mysqli_error($mysqli)));
            $mysqli->query('rollback');
            return;
        }
        //提交事务
        $mysqli->query('commit');
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
            echo json_encode(array('type'=>'error','msg'=>"写入配置文件时出现错误:\n".$e->getMessage()));
            return;
        }
        echo json_encode(array('type'=>'success','msg'=>'安装完成，正在跳转至主页。'));
    } else {
        echo json_encode(array('type'=>'error','msg'=>'提交的参数错误。'));
    }
} else {
    //非执行页面
    include_once('../function.php');
    include_once('./install.default.php');
}?>