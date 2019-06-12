<?php
$u = trim($_GET['u']);
$error_code = 200;
//优先查询普通
$id = f62to10($u);
$stmt = $mysqli->prepare('SELECT url,expires FROM shortlinks WHERE id=?');
$stmt->bind_param('i',$id);
$stmt->bind_result($url, $expires);
$stmt->execute();
if (!$stmt){
    $error_code = 300;  //普通跳转查询错误
    include_once ('./index.error.php');
    return;
}
$stmt->store_result();
if ($stmt->num_rows() > 0){
    $stmt->fetch();
    $stmt->close();
    if ($expires != null){
        if ($expires >= time()){
            header('Location: '.$url);
            location_stat($id,false);
        } else {
            $error_code = 303;   //链接过期，不能访问
            include_once('./index.error.php');
        }
    } else {
        header('Location: '.$url);
        location_stat($id,false);
    }
    return;
} else {
    $stmt->close();
}
//普通未查询到，查询自定义
$stmt = $mysqli->prepare('SELECT id,url,expires FROM shortlinks_custom WHERE cname=?');
$stmt->bind_param('s', $u);
$stmt->bind_result($cid, $url, $expires);
$stmt->execute();
if (!$stmt){
    $error_code = 301;  //自定义跳转查询错误
    include_once ('./index.error.php');
    return;
}
$stmt->store_result();
if ($stmt->num_rows() > 0){
    $stmt->fetch();
    $stmt->close();
    if ($expires != null){
        if ($expires >= time()){
            header('Location: '.$url);
            location_stat($cid, true);
        } else {
            $error_code = 303;   //链接过期，不能访问
            include_once('./index.error.php');
        }
    } else {
        header('Location: '.$url);
        location_stat($cid, true);
    }
    return;
} else {
    $stmt->close();
    $error_code = 304;  //无法找到
    include_once('./index.error.php');
}

function location_stat($id, $iscustom){
    global $mysqli;
    $custom = $iscustom?"custom_":"";
    $stmt = $mysqli->query("UPDATE shortlinks_".$custom."log SET count=count+'1', lasttime=".time()." WHERE linkid=?;");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if (!$stmt){
        return;
    }
}
?>