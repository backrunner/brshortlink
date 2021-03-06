<?php
if (file_exists('../install.lock')) {
    session_start();
    include_once '../config.php';
    include_once '../sqlconn.php';
    include_once '../safe.php';
    require_once '../function.php';
    if ($mysqli->connect_errno){
        echo json_encode(array('type' => 'error', 'code' => 100, 'error' => '无法连接至数据库。'));
        return;
    }
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                //参数检查
                if (!isset($_POST['username'])){
                    echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
                    return;
                }
                if (!isset($_POST['password'])){
                    echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
                }
                //安全过滤
                $u = fn_safe($_POST['username']);
                $p = fn_safe($_POST['password']);
                //从数据库查询密码
                $query_login = $mysqli->prepare('SELECT id,password FROM managers WHERE username=?;');
                $query_login->bind_param('s', $u);
                $query_login->bind_result($userid, $pwd);
                $query_login->execute();
                if (!$query_login) {
                    echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '查询执行错误。'));
                    return;
                }
                $query_login->store_result();
                if ($query_login->num_rows() > 0) {
                    $query_login->fetch();
                    $query_login->close();
                    if ($pwd === hash('sha256',$p)) {
                        //密码比对正确
                        $_SESSION['admin'] = true;
                        $_SESSION['username'] = $u;
                        echo json_encode(array('type' => 'success', 'code' => 200, 'message' => '登录成功。'));
                        //执行记录
                        $query_loginlog = $mysqli->prepare('insert into managers_log (userid, logintime, loginip) values (?,?,?)');
                        //获取信息
                        $time = time();
                        $ip = getIP();
                        //参数绑定
                        $query_loginlog->bind_param('iis', $userid, $time, $ip);
                        $query_loginlog->execute();
                        $query_loginlog->close();
                    } else {
                        echo json_encode(array('type' => 'error', 'code' => 401, 'error' => '用户名或密码错误。'));
                    }
                } else {
                    $query_login->close();
                    echo json_encode(array('type' => 'error', 'code' => 401, 'error' => '用户名或密码错误。'));
                }
                break;
            case 'logout':
                if (!isset($_SESSION['admin']) || !$_SESSION['admin']){
                    echo json_encode(array('type' => 'error', 'code' => 101, 'error' => '权限错误。'));
                    return;
                }
                if (!isset($_POST['username'])){
                    echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
                    return;
                }
                if ($_POST['username'] == $_SESSION['username']){
                    session_destroy();
                    echo json_encode(array('type' => 'error', 'code' => 200, 'error' => '登出成功。'));
                } else {
                    echo json_encode(array('type' => 'error', 'code' => 301, 'error' => '参数错误。'));
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
