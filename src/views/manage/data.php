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
    if (!isset($_POST['type'])){
        echo json_encode(array('type' => 'error', 'code' => 300, 'error' => '请提交完整的参数。'));
        return;
    }
    $type = fn_safe($_POST['type']);
    switch ($type){
        case 'totalLinkCount':
            $query_totalLinkCount = 'SELECT count(*) as total FROM shortlinks';
            $query_totalCustomLinkCount = 'SELECT count(*) as total FROM shortlinks_custom';
            $res_totalLinkCount = $mysqli->query($query_totalLinkCount);
            if (!$res_totalLinkCount){
                echo json_encode(array('type' => 'error', 'code' => 102, 'error' => '查询时出现错误。'));
                return;
            }
            $res_totalLinkCount = $res_totalLinkCount->fetch_array();
            $totalLinkCount = $res_totalLinkCount['total'];
            $res_totalCustomLinkCount = $mysqli->query($query_totalCustomLinkCount);
            if (!$res_totalCustomLinkCount){
                echo json_encode(array('type' => 'error', 'code' => 102, 'error' => '查询时出现错误。'));
                return;
            }
            $res_totalCustomLinkCount = $res_totalCustomLinkCount->fetch_array();
            $totalCustomLinkCount = $res_totalCustomLinkCount['total'];
            echo json_encode(array('type' => 'success', 'code' => 200, 'data' => $totalLinkCount+$totalCustomLinkCount));
            break;
        case 'totalClick':
            $query_totalClick = 'SELECT sum(count) as total FROM shortlinks_log';
            $query_totalCustomClick = 'SELECT sum(count) as total FROM shortlinks_custom_log';
            $res_totalClick = $mysqli->query($query_totalClick);
            if (!$res_totalClick){
                echo json_encode(array('type' => 'error', 'code' => 102, 'error' => '查询时出现错误。'));
                return;
            }
            $res_totalClick = $res_totalClick->fetch_array();
            $totalClick = $res_totalClick['total'];
            $res_totalCustomClick = $mysqli->query($query_totalCustomClick);
            if (!$res_totalCustomClick){
                echo json_encode(array('type' => 'error', 'code' => 102, 'error' => '查询时出现错误。'));
                return;
            }
            $res_totalCustomClick = $res_totalCustomClick->fetch_array();
            $totalCustomClick = $res_totalCustomClick['total'];
            echo json_encode(array('type' => 'success', 'code' => 200, 'data' => $totalClick+$totalCustomClick));
            break;
        case 'user':
            if (isset($_POST['limit'])||isset($_POST['offset'])){
                $query_user = $mysqli->prepare('select id, username, ctime from managers limit ?,?');
                $limit = fn_safe($_POST['limit']);
                $offset = fn_safe($_POST['offset']);
                $query_user->bind_param('ii', $offset, $limit);
                $query_user->execute();
                $query_user->bind_result($id, $username,$ctime);
                $res_user = array();
                while ($query_user->fetch()) {
                    $t = array('id'=>$id,'username'=>$username,'ctime'=>$ctime);
                    array_push($res_user, $t);
                }
                $query_user->close();
                $query_total = $mysqli->query('select count(*) as total from managers');
                $res_total = $query_total->fetch_array();
                echo json_encode(array('rows'=>$res_user, 'total'=>$res_total['total']));
            }
            break;
        case 'userlog':
            if (isset($_POST['limit']) || isset($_POST['offset'])){
                $query_userlog = $mysqli->prepare('select managers_log.id, managers_log.userid, username, logintime, loginip from managers_log, managers where managers_log.userid=managers.id order by managers_log.id desc limit ?,?');
                $limit = fn_safe($_POST['limit']);
                $offset = fn_safe($_POST['offset']);
                $query_userlog->bind_param('ii', $offset, $limit);
                $query_userlog->bind_result($id, $userid,$username,$logintime,$loginip);
                $query_userlog->execute();
                $res_userlog = array();
                while ($query_userlog->fetch()) {
                    $t = array('id'=>$id,'userid'=>$userid,'username'=>$username,'logintime'=>$logintime,'loginip'=>$loginip);
                    array_push($res_userlog, $t);
                }
                $query_userlog->close();
                $query_total = $mysqli->query('select count(*) as total from managers_log');
                $res_total = $query_total->fetch_array();
                echo json_encode(array('rows'=>$res_userlog, 'total'=>$res_total['total']));
            }
            break;
        case 'shortlink':
            if (isset($_POST['limit']) || isset($_POST['offset'])){
                $query_shortlink = $mysqli->prepare('select shortlinks.id, url, ctime, expires, count, lasttime from shortlinks, shortlinks_log where shortlinks.id=shortlinks_log.linkid order by shortlinks.id desc limit ?,?');
                $limit = fn_safe($_POST['limit']);
                $offset = fn_safe($_POST['offset']);
                $query_shortlink->bind_param('ii', $offset, $limit);
                $query_shortlink->bind_result($id, $url,$ctime,$expires,$count,$lasttime);
                $query_shortlink->execute();
                $res_shortlink = array();
                while ($query_shortlink->fetch()) {
                    $t = array('id'=>$id,'url'=>$url,'ctime'=>$ctime,'expires'=>$expires,'count'=>$count,'lasttime'=>$lasttime);
                    array_push($res_shortlink, $t);
                }
                $query_shortlink->close();
                $query_total = $mysqli->query('select count(*) as total from shortlinks');
                $res_total = $query_total->fetch_array();
                echo json_encode(array('rows'=>$res_shortlink, 'total'=>$res_total['total']));
            }
            break;
        case 'customlink':
            if (isset($_POST['limit']) || isset($_POST['offset'])){
                $query_customlink = $mysqli->prepare('select shortlinks_custom.id, cname, url, ctime, expires, count, lasttime from shortlinks_custom, shortlinks_custom_log where shortlinks_custom.id=shortlinks_custom_log.linkid order by shortlinks_custom.id desc limit ?,?');
                $limit = fn_safe($_POST['limit']);
                $offset = fn_safe($_POST['offset']);
                $query_customlink->bind_param('ii', $offset, $limit);
                $query_customlink->bind_result($id,$cname,$url,$ctime,$expires,$count,$lasttime);
                $query_customlink->execute();
                $res_customlink = array();
                while ($query_customlink->fetch()) {
                    $t = array('id'=>$id,'url'=>$url,'ctime'=>$ctime,'expires'=>$expires,'count'=>$count,'lasttime'=>$lasttime);
                    array_push($res_customlink, $t);
                }
                $query_customlink->close();
                $query_total = $mysqli->query('select count(*) as total from shortlinks');
                $res_total = $query_total->fetch_array();
                echo json_encode(array('rows'=>$res_customlink, 'total'=>$res_total['total']));
            }
            break;
        default:
            echo json_encode(array('type' => 'error', 'code' => 301, 'error' => '参数错误。'));
            break;
    }
}