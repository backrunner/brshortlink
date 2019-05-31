<?php
if (file_exists('../install.lock')){
    session_start();
    include_once('../config.php');
    include_once('../function.php');
    include_once('../safe.php');
    $admin = false;
    if (isset($_SESSION['admin']) && isset($_SESSION['username']) && $_SESSION['admin']===true){
        include_once('./dashboard.php');
    } else {
        //没有action，加载普通页面
        include_once('./login.php');
    }
} else {
    header('Location: /install');
}