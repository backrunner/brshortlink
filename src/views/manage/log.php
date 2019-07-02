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
    if(!isset($_POST['action'])){
        switch($_POST['action']){
            case 'deletelog':
            if (isset($_POST['day']) && isset($_POST['type'])){
                $day = fn_safe($_POST['day']);
                $type = $_POST['type'];
                switch($type){
                    case 'accesslog':
                    if (($_POST['day']) == 'all'){
                        $res = $mysqli->query('TRUNCATE TABLE access_log');
                        if ($res){
                            echo json_encode(array('type' => 'success', 'code' => 200));
                        } else {
                            echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                        }
                    } else if ($_POST['day'] == '30'){
                        $res = $mysqli->query('DELETE FROM access_log WHERE time<'.time()-2592000);
                        if ($res){
                            echo json_encode(array('type' => 'success', 'code' => 200));
                        } else {
                            echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                        }
                    } else if ($_POST['day'] == '7'){
                        $res = $mysqli->query('DELETE FROM access_log WHERE time<'.time()-604800);
                        if ($res){
                            echo json_encode(array('type' => 'success', 'code' => 200));
                        } else {
                            echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                        }
                    }
                    break;
                    case 'userlog':
                    if (($_POST['day']) == 'all'){
                        $res = $mysqli->query('TRUNCATE TABLE managers_log');
                        if ($res){
                            echo json_encode(array('type' => 'success', 'code' => 200));
                        } else {
                            echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                        }
                    } else if ($_POST['day'] == '30'){
                        $res = $mysqli->query('DELETE FROM managers_log WHERE time<'.time()-2592000);
                        if ($res){
                            echo json_encode(array('type' => 'success', 'code' => 200));
                        } else {
                            echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                        }
                    } else if ($_POST['day'] == '7'){
                        $res = $mysqli->query('DELETE FROM managers_log WHERE time<'.time()-604800);
                        if ($res){
                            echo json_encode(array('type' => 'success', 'code' => 200));
                        } else {
                            echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                        }
                    }
                    break;
                }
            } else {
                echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
            }
            break;
            default:
            echo json_encode(array('type' => 'error', 'code' => 301, 'error' => '参数错误。'));
            break;
        }
    } else {
        echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
    }
}