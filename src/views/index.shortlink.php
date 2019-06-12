<?php
if ($_GET['action'] == 'shortlink'){
    if (isset($_GET['link_type']) && isset($_GET['long_link'])){
        $long_link = trim($_GET['long_link']);
        if (!preg_match("/(https?|ftp|file|steam):\/\/[-A-Za-z0-9+&@#\/%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/%=~_|]/", $long_link)){
            echo json_encode(array('type'=>'error','error_code'=>404,'error'=>'请勿提交非法URL。'));
            return;
        }
        if ($_GET['link_type'] == 'normal'){
            //普通链接
            $link_hash = sha1($long_link);  //先计算hash，后面会用到
            if (isset($_GET['expires'])){
                //有过期时间的不查重
                $exp = intval($_GET['expires']);
                if (check_expires($exp)){
                    //预处理插入
                    $stmt = $mysqli->prepare('INSERT INTO shortlinks (url, urlhash, ctime, expires) VALUES (?,?,?,?);');
                    $nowtime = time();
                    $stmt->bind_param('ssii',$long_link, $link_hash, $nowtime, $exp);
                    $res = $stmt->execute();
                    if ($res){
                        //插入执行成功
                        echo json_encode(array('type'=>'success','short_link'=>mysqli_insert_id($mysqli)));
                    } else {
                        echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                    }
                    $stmt->close();
                } else {
                    echo json_encode(array('type'=>'error','error_code'=>402,'error'=>'过期时间错误。'));
                }
            } else {
                //没有过期时间，查重
                $query_existed = $mysqli->prepare("SELECT id FROM shortlinks WHERE urlhash=? AND expires is null;");
                $query_existed->bind_param('s', $link_hash);
                $query_existed->bind_result($link_id);
                $query_existed->execute();
                if (!$query_existed){
                    echo json_encode(array('type'=>'error','error_code'=>411,'error'=>'查重时出现错误。'));
                    return;
                }
                $query_existed->store_result();
                if ($query_existed->num_rows() > 0){
                    $query_existed->fetch();
                    echo json_encode(array('type'=>'success', 'short_link'=>$link_id));
                    $query_existed->close();
                    return;
                }
                $query_existed->close();
                //准备插入
                $mysqli->query('begin');
                $query_insert = $mysqli->prepare("INSERT INTO shortlinks (url, urlhash, ctime) VALUES (?,?,?);");
                $nowtime=time();
                $query_insert->bind_param('ssi',$long_link, $link_hash, $nowtime);
                $res = $query_insert->execute();
                if (!$query_insert){
                    $mysqli->query('rollback');
                    echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                    return;
                }
                $query_insert->close();
                $linkid = mysqli_insert_id($mysqli);
                $res_insertlog = $mysqli->query('INSERT INTO shortlinks_log (linkid,count) VALUES('.$linkid.',0);');
                if (!$res_insertlog){
                    $mysqli->query('rollback');
                    echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                    return;
                }
                $mysqli->query('commit');
                echo json_encode(array('type'=>'success','short_link'=>$linkid));
            }
        } else if ($_GET['link_type'] == 'custom'){
            if (isset($_GET['custom_link'])){
                //自定义短链接
                $custom_link = trim($_GET['custom_link']);
                //正则测试
                preg_match("/^[A-Za-z0-9]+$/",$custom_link, $real_custom_link);
                if (count($real_custom_link) <= 0){
                    echo json_encode(array('type'=>'error','error_code'=>407,'error'=>'校验自定义短链接时出错。'));
                    return;
                }
                if ($real_custom_link[0] != $custom_link){
                    echo json_encode(array('type'=>'error','error_code'=>405,'error'=>'自定义短链接不合法。'));
                    return;
                }
                //测试是否为保留字
                $reserved = ['manage','install','static'];
                if (in_array($custom_link, $reserved)){
                    echo json_encode(array('type'=>'error','error_code'=>406,'error'=>'请勿使用保留字作为自定义短链接。'));
                    return;
                }
                //查询是否被占用
                $query_existed = $mysqli->prepare('SELECT id FROM `shortlinks_custom` WHERE cname=?;');
                $query_existed->bind_param('s', $custom_link);
                $query_existed->execute();
                if (!$query_existed){
                    echo json_encode(array('type'=>'error','error_code'=>412,'error'=>'查重时出现错误。'));
                    return;
                }
                $query_existed->store_result();
                if ($query_existed->num_rows() > 0){
                    echo json_encode(array('type'=>'error','error_code'=>403,'error'=>'该自定义短链接已被占用。'));
                    $query_existed->close();
                    return;
                }
                $query_existed->close();
                if (isset($_GET['expires'])){
                    $exp = intval($_GET['expires']);
                    if (check_expires($exp)){
                        //预处理插入
                        $mysqli->query('begin');
                        $query_insert_custom = $mysqli->prepare('INSERT INTO shortlinks_custom (cname, `url`, ctime, expires) VALUES (?,?,?,?);');
                        $nowtime = time();
                        $query_insert_custom->bind_param('ssii',$custom_link, $long_link, $nowtime, $exp);
                        $res = $query_insert_custom->execute();
                        if (!$res){
                            $mysqli->query('rollback');
                            echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                            return;
                        }
                        $query_insert_custom->close();
                        $linkid = mysqli_insert_id($mysqli);
                        $res_insertlog = $mysqli->query('INSERT INTO shortlinks_custom_log (linkid,count) VALUES('.$linkid.',0);');
                        if (!$res_insertlog){
                            $mysqli->query('rollback');
                            echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                            return;
                        }
                        $mysqli->query('commit');
                        //插入执行成功
                        echo json_encode(array('type'=>'success','short_link'=>$custom_link));
                    } else {
                        echo json_encode(array('type'=>'error','error_code'=>402,'error'=>'过期时间错误。'));
                    }
                } else {
                    //预处理插入
                    $mysqli->query('begin');
                    $query_insert_custom = $mysqli->prepare('INSERT INTO shortlinks_custom (cname, `url`, ctime) VALUES (?,?,?);');
                    $nowtime = time();
                    $query_insert_custom->bind_param('ssi',$custom_link, $long_link, $nowtime);
                    $res = $query_insert_custom->execute();
                    if (!$res){
                        $mysqli->query('rollback');
                        echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                        return;
                    }
                    $query_insert_custom->close();
                    $linkid = mysqli_insert_id($mysqli);
                    $res_insertlog = $mysqli->query('INSERT INTO shortlinks_custom_log (linkid,count) VALUES('.$linkid.',0);');
                    if (!$res_insertlog){
                        $mysqli->query('rollback');
                        echo json_encode(array('type'=>'error','error_code'=>410,'error'=>'插入短链接到数据库失败。'));
                        return;
                    }
                    $mysqli->query('commit');
                }
            } else {
                echo json_encode(array('type'=>'error','error_code'=>401,'error'=>'提交的参数不完整。'));
            }
        } else {
            echo json_encode(array('type'=>'error','error_code'=>400,'error'=>'参数错误。'));
        }
    } else {
        echo json_encode(array('type'=>'error','error_code'=>401,'error'=>'提交的参数不完整。'));
    }
} else {
    echo json_encode(array('type'=>'error','error_code'=>400,'error'=>'参数错误。'));
}

function check_expires($expires){
    if ($expires <= time()){
        return false;
    }
    return true;
}
?>