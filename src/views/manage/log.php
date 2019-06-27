<?php
if (file_exists('../install.lock')) {
    session_start();
    include_once '../config.php';
    include_once '../sqlconn.php';
    include_once '../safe.php';
    $admin = false;
    if ($mysqli->connect_errno) {
        echo json_encode(array('type' => 'error', 'code' => 100, 'error' => '无法连接至数据库。'));
        return;
    }
    if (!isset($_SESSION['admin']) || !isset($_SESSION['user']) && !$_SESSION['admin']) {
        echo json_encode(array('type' => 'error', 'code' => 101, 'error' => '权限错误。'));
        return;
    }
}