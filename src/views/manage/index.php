<?php
if (file_exists('../install.lock')){
    include_once('../config.php');
    session_start();
    $session_id = session_id();
    setcookie('PHPSESSID',$session_id,time()+30*24*3600);
    if (isset($_SESSION['manager'])){

    } else {
        if (isset($_POST['action'])){
            
        } else {
            //没有action，加载普通页面
            include_once('./manage.default.php');
        }
    }
} else {
    header('Location: /install');
}