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
    if (!isset($_POST['type']) || !isset($_POST['action'])) {
        echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
        return;
    }
    switch ($action) {
        case 'cleanOutdate':
            $query = '';
            if ($_POST['type']=='normal'){
                $query = 'DELETE FROM shortlinks_custom WHERE id in (SELECT id FROM shortlinks WHERE not isnull(expires) and expires>?)';
            } else if ($_POST['type']=='custom'){
                $query = 'DELETE FROM shortlinks_custom WHERE id in (SELECT id FROM shortlinks_custom WHERE not isnull(expires) and expires>?)';
            } else {
                echo json_encode(array('type' => 'error', 'code' => 301, 'error' => '参数错误。'));
                return;
            }
            $n_time = time();
            $mysqli->query('begin');
            $query_delete = $mysqli->prepare($query);
            $query_delete->bind_param('i', $n_time);
            $query_delete->bind_result($id);
            $res_delete = $query_delete->execute();
            if ($res_delete) {
                echo json_encode(array('type' => 'success', 'code' => 200));
            } else {
                echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
            }
            $query_delete->close();
            break;
        case 'delete':
            if (isset($_POST['linkid']) && isset($_POST['type'])){
                $linkid = fn_safe($_POST['linkid']);
                $q = '';
                if ($_POST['type']=='normal'){
                    $q = 'DELETE FROM shortlinks WHERE id=?';
                } else if ($_POST['type']=='custom') {
                    $q = 'DELETE FROM shortlinks_custom WHERE id=?';
                }
                $query_delete = $mysqli->prepare($q);
                $query_delete->bind_param('i',$linkid);
                $res_delete = $query_delete->execute();
                if ($res_delete){
                    echo json_encode(array('type' => 'success', 'code' => 200));
                } else {
                    echo json_encode(array('type' => 'error', 'code' => 400, 'error' => '数据操作执行错误。'));
                }
                $res_delete->close();
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