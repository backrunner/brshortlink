<?php
if (file_exists('./install.lock')){
    include_once('./config.php');
    include_once('./function.php');

    if ($mysqli->connect_errno){
        $error_code = 100;  //无法连接到数据库
        include_once('./index.error.php');
        return;
    }

    if (isset($_GET['u'])){
    //跳转
        include_once('./index.location.php');
    } else if (isset($_GET['action'])){
    //动作 -> 创建短链接
        include_once('./index.shortlink.php');
    } else {
        //默认首页
        include_once('./index.default.php');
    }
} else {
    //尚未安装
    header("Location:/install");
}
?>