<?php if (file_exists('./install.lock')){
    
} else {
    //尚未安装
    header("Location:/install");
}
?>