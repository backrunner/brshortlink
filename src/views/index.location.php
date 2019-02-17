<?php
$u = trim($_GET['u']);
$error_code = 200;
//优先查询普通
$id = f62to10($u);
echo $id;
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
            if (STAT_ENABLED){
                location_stat($id,false);
            }
        } else {
            $error_code = 303;   //链接过期，不能访问
            include_once('./index.error.php');
        }
    } else {
        header('Location: '.$url);
        if (STAT_ENABLED){
            location_stat($id,false);
        }
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
            if (STAT_ENABLED){
                location_stat($cid, true);
            }
        } else {
            $error_code = 303;   //链接过期，不能访问
            include_once('./index.error.php');
        }
    } else {
        header('Location: '.$url);
        if (STAT_ENABLED){
            location_stat($cid, true);
        }
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
    $stmt = $mysqli->prepare("SELECT id FROM shortlinks_".$custom."log WHERE linkid=?;");
    $stmt->bind_param('i', $id);
    $stmt->bind_result($logid);
    $stmt->execute();
    if (!$stmt){
        return;
    }
    $stmt->store_result();
    if ($stmt->num_rows() > 0){
        $stmt->fetch();
        $stmt->close();
        $mysqli->query("UPDATE shortlinks_".$custom."log SET count=count+'1', lasttime=".time()." WHERE id=".$logid.";");
    } else {
        $stmt->close();
        $stmt=$mysqli->prepare('INSERT INTO shortlinks_'.$custom.'log (linkid,count,lasttime) VALUES(?,?,?);');
        $nowtime = time();
        $init_count = 1;
        $stmt->bind_param("iii",$id,$init_count,$nowtime);
        $stmt->execute();
        $stmt->close();
    }
}
?>