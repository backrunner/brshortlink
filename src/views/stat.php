<?php
if (file_exists('./install.lock')){
    include_once('./config.php');
    include_once('./sqlconn.php');

    
} else {
    //尚未安装
    header("Location:/install");
}