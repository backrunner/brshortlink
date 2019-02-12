<?php
if (file_exists('./install.lock')){
    include_once('./config.php');
    include_once('./function.php');
    if (isset($_GET['u'])){
    //跳转
        echo 'GET Parament: u - '.$_GET['u'];
    } else if (isset($_GET['action'])){
    //动作 -> 创建短链接
        if ($_GET['action'] == 'shortlink'){
            if (isset($_GET['link_type']) && isset($_GET['long_link'])){
                if ($_GET['link_type'] == 'normal'){

                } else if ($_GET['link_type'] == 'custom'){
                    if (isset($_GET['custom_link'])){

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
    } else {
    //默认首页
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?php echo SITE_NAME; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../static/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/main.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/toastr.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/awesome-bootstrap-checkbox.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
    <script src="../static/crypto-js.js"></script>
    <script src="../static/index.function.js"></script>
</head>

<body>
    <div class="container index-container" id="main-container">
        <div class="row">
            <div class="col-lg-12 page-header">
                <h2><?php displayTitle();?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 header">
                <h3>生成短网址</h3>
            </div>
            <div class="col-lg-12">
                <div class="form-group link-form">
                    <label style="display:inline-block">URL</label>
                    <div class="link-apichoice">
                        <div class="abc-radio abc-radio-primary link-apiradio">
                            <input type="radio" id="r-localsite" name="r-api">
                            <label for="r-localsite">
                                <?php echo $_SERVER['HTTP_HOST'];?>
                            </label>
                        </div>
                        <div class="abc-radio abc-radio-primary link-apiradio" style="padding-left:12px;">
                            <input type="radio" id="r-sinaapp" name="r-api">
                            <label for="r-sinaapp">
                                t.cn
                            </label>
                        </div>
                    </div>
                    <input type="text" class="form-control" id="i-link">
                </div>
                <div class="abc-checkbox abc-checkbox-primary customlink-checkbox">
                    <input type="checkbox" id="cb_customlink">
                    <label for="cb_customlink">
                        自定义短链接
                    </label>
                </div>
                <div class="col-sm-6 customlink-input">
                    <input type="text" id="i-customlink" class="form-control">
                </div>
                <div class="link-generatebtn">
                    <button class="btn btn-primary" id="btn-generate" onclick="checkSubmit();">生成</button>
                </div>
            </div>
        </div>
        <div class="row row-generated">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <span>你的短链接</span>
                    </div>
                    <div class="card-body" id="link-card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        //api
        var api = getCookie('api-choice');
        var use_custom_link = false;
        //初始化
        $(document).ready(function(){
            if (api == null){
                api = 0;
                $('#r-localsite').prop("checked",true);
            } else if (api == "0"){
                $('#r-localsite').prop("checked",true);
                if ($('#cb_customlink').attr('disabled') != undefined){
                    $('#cb_customlink').removeAttr('disabled');
                }
            } else if (api == "1"){
                $('#r-sinaapp').prop("checked",true);
                $('#cb_customlink').attr('disabled','disabled');
            }
            var customlink_status = getCookie('customlink-status');
            if (customlink_status == '1'){
                $('#cb_customlink').prop("checked",true);
                if ($(window).width()>=576){
                    $('.customlink-input').attr('style', 'display: inline-block;');
                } else {
                    $('.customlink-checkbox').attr('style','display: block');
                    $('.customlink-input').attr('style', 'display: block;');
                }
            }
            //绑定事件
            $('#cb_customlink').change(function(){
                use_custom_link = !use_custom_link;
                if ($('.customlink-input').attr('style') != undefined){
                    if ($(window).width() < 576){
                        $('.customlink-checkbox').removeAttr('style');
                    }
                    $('.customlink-input').removeAttr('style');
                    customlink_status = 0;
                    setCookie('customlink-status', customlink_status, 30);
                } else {
                    if ($(window).width()>=576){
                        $('.customlink-input').attr('style', 'display: inline-block;');
                    } else {
                        $('.customlink-checkbox').attr('style','display: block');
                        $('.customlink-input').attr('style', 'display: block;');
                    }
                    customlink_status = 1;
                    setCookie('customlink-status', customlink_status, 30);
                }
            });
            $('#r-localsite').click(function(){
                api = 0;
                setCookie('api-choice', api, 30);
                if ($('#cb_customlink').attr('disabled') != undefined){
                    $('#cb_customlink').removeAttr('disabled');
                }
            });
            $('#r-sinaapp').click(function(){
                api = 1;
                setCookie('api-choice', api, 30);
                $('#cb_customlink').attr('disabled','disabled');
            });
        });
    </script>
</body>
</html>
<?php }
} else {
    //尚未安装
    header("Location:/install");
}
?>