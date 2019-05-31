<?php
if (file_exists('../install.lock')) {
    session_start();
    include_once '../config.php';
    include_once '../sqlconn.php';
    include_once '../safe.php';
    if ($mysqli->connect_errno){
        echo json_encode(array('type' => 'error', 'code' => 100, 'error' => '无法连接至数据库。'));
        return;
    }
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                //安全过滤
                $u = fn_safe($_POST['username']);
                //从数据库查询密码
                $query_login = $mysqli->prepare('SELECT password FROM managers WHERE username=?;');
                $query_login->bind_param('s', $u);
                $query_login->bind_result($pwd);
                $query_login->execute();
                if (!$query_login) {
                    echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '查询执行错误。'));
                    return;
                }
                $query_login->store_result();
                if ($query_login->num_rows() > 0) {
                    $query_login->fetch();
                    $query_login->close();
                    if ($pwd === $_POST['password']) {
                        //密码比对正确
                        $_SESSION['admin'] = true;
                        $_SESSION['username'] = $u;
                        echo json_encode(array('type' => 'success', 'code' => 200, 'message' => '登录成功。'));
                    } else {
                        echo json_encode(array('type' => 'error', 'code' => 401, 'error' => '用户名或密码错误。'));
                    }
                } else {
                    $query_login->close();
                    echo json_encode(array('type' => 'error', 'code' => 401, 'error' => '用户名或密码错误。'));
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                break;
        }
    }
} else {
    header('HTTP/1.1 404 Not Found');
}
