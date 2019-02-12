<?php
if (file_exists('./install.lock')){
    include_once('./config.php');
    include_once('./function.php');
    if (isset($_GET['u'])){
    //跳转
        echo 'GET Parament: u - '.$_GET['u'];
    } else if (isset($_GET['action'])){
    //动作 -> 创建短链接
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
                <h2>B<mhide>ack</mhide>R<mhide>unner</mhide>'s ShortLink</h2>
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
                    <button class="btn btn-primary">生成</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        //api
        var api = getCookie('api-choice');
        //初始化
        if (api == null){
            api = 0;
            $('#r-localsite').prop("checked",true);
        } else if (api == "0"){
            $('#r-localsite').prop("checked",true);
        } else if (api == "1"){
            $('#r-sinaapp').prop("checked",true);
        }
        var customlink_status = getCookie('customlink-status');
        if (customlink_status == '1'){
            $('#cb_customlink').prop("checked",true);
            $('#i-customlink').attr('style', 'display: inline-block;');
        }
        //绑定事件
        $('#cb_customlink').change(function(){
            if ($('#i-customlink').attr('style') != undefined){
                $('#i-customlink').removeAttr('style');
                customlink_status = 0;
                setCookie('customlink-status', customlink_status, 30);
            } else {
                $('#i-customlink').attr('style', 'display: inline-block;');
                customlink_status = 1;
                setCookie('customlink-status', customlink_status, 30);
            }
        });
        $('#r-localsite').click(function(){
            api = 0;
            setCookie('api-choice', api, 30);
        });
        $('#r-sinaapp').click(function(){
            api = 1;
            setCookie('api-choice', api, 30);
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