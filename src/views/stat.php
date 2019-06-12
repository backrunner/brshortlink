<?php
if (file_exists('./install.lock')){
    include_once('./config.php');
    include_once('./sqlconn.php');
    require_once('./function.php');
    //PV
    if (!isset($_COOKIE["BRSTAT_FLAG"])){
        $mysqli->query("UPDATE access SET COUNT=COUNT+1 WHERE type='pv'");
        setcookie("BRSTAT_FLAG", sha1(strval(time())),time()+300);
    }
    //UV
    if (!isset($_COOKIE["BRSTAT"])){
        $realip = getip();
        setcookie("BRSTAT",base64_encode($realip.strval(time())), time()+86400);
        $mysqli->query("begin");
        $query_insertLog = $mysqli->prepare("INSERT access_log (time, ip) VALUES (?,?) ");
        $query_insertLog->bind_param('is',time(),$realip);
        $res_insertLog = $query_insertLog->execute();
        if (!$res_insertLog){
            $mysqli->query("rollback");
        }
        $query_insertLog->close();
        $res_updateCount = $mysqli->query("UPDATE access SET COUNT=COUNT+1 WHERE type='uv'");
        $mysqli->query("commit");
    }
} else {
    //尚未安装
    header("Location:/install");
}