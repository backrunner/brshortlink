<?php
if (file_exists('../install.lock')) {
    session_start();
    include_once '../config.php';
    include_once '../sqlconn.php';
    include_once '../safe.php';
    $admin = false;
    if ($mysqli->connect_errno){
        echo json_encode(array('type' => 'error', 'code' => 100, 'error' => '无法连接至数据库。'));
        return;
    }
    if (!isset($_SESSION['admin']) || !isset($_SESSION['user']) && ! $_SESSION['admin']){
        echo json_encode(array('type' => 'error', 'code' => 101, 'error' => '权限错误。'));
        return;
    }
    if (!isset($_POST['action'])){
        echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
        return;
    }
    $action = $_POST['action'];
    switch ($action){
        case 'add':
            if (isset($_POST['username']) && isset($_POST['password'])){
                
            }
            break;
        case 'edit':
            break;
        case 'delete':
            break;
        default:
            echo json_encode(array('type' => 'error', 'code' => 301, 'error' => '参数错误。'));
            break;
    }
}