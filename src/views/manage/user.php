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
    if (!isset($_POST['action'])) {
        echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
        return;
    }
    $action = $_POST['action'];
    switch ($action) {
        case 'add':
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = fn_safe($_POST['username']);
                $password = hash('sha256', fn_safe($_POST['password']));
                $query_adduser = $mysqli->prepare('INSERT INTO managers (username, password, ctime) VALUES (?, ?, ?)');
                $n_time = time();
                $query_adduser->bind_param('ssi', $username, $password, $n_time);
                $res = $query_adduser->execute();
                if ($res) {
                    echo json_encode(array('type' => 'success', 'code' => 200));
                } else {
                    echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                }
                $query_adduser->close();
            } else {
                echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
                return;
            }
            break;
        case 'edit':
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = fn_safe($_POST['username']);
                $password = hash('sha256', fn_safe($_POST['password']));
                $query_update = $mysqli->prepare('UPDATE managers SET password=? WHERE username=?');
                $query_update = $mysqli->bind_param('ss', $password, $username);
                $res_update = $query_update->execute();
                if ($res_update) {
                    echo json_encode(array('type' => 'success', 'code' => 200));
                } else {
                    echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                }
                $query_update->close();
            } else {
                echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
                return;
            }
            break;
        case 'delete':
            if (isset($_POST['username'])) {
                $username = fn_safe($_POST['username']);
                $query_delete = $mysqli->prepare('DELETE FROM managers WHERE username=?');
                $query_delete->bind_param('s', $username);
                $res = $query_delete->execute();
                if ($res) {
                    echo json_encode(array('type' => 'success', 'code' => 200));
                } else {
                    echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                }
                $query_delete->close();
            } else {
                echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
                return;
            }
            break;
        default:
            echo json_encode(array('type' => 'error', 'code' => 301, 'error' => '参数错误。'));
            break;
    }
}
