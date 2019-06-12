<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?php echo SITE_NAME; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="static/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="static/main.min.css" />
    <link rel="stylesheet" type="text/css" href="static/toastr.min.css" />
    <link rel="stylesheet" type="text/css" href="static/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="static/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="static/tempusdominus.min.css" />
    <script src="static/jquery.min.js"></script>
    <script src="static/bootstrap.min.js"></script>
    <script src="static/toastr.min.js"></script>
    <script src="static/crypto-js.js"></script>
    <script src="static/moment.min.js"></script>
    <script src="static/tempusdominus.min.js"></script>
    <script src="static/index.function.js"></script>
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
                <h3>生成短链接</h3>
            </div>
            <div class="col-lg-12">
                <div class="form-group link-form">
                    <label style="display:inline-block">URL</label>
                    <div class="link-apichoice">
                        <div class="custom-control custom-radio link-apiradio radio-localsite">
                            <input type="radio" class="custom-control-input" id="r-localsite" name="r-api">
                            <label class="custom-control-label" for="r-localsite">
                                <?php echo $_SERVER['HTTP_HOST'];?>
                            </label>
                        </div>
                        <div class="custom-control custom-radio link-apiradio">
                            <input type="radio" class="custom-control-input" id="r-sinaapp" name="r-api">
                            <label class="custom-control-label" for="r-sinaapp">
                                t.cn
                            </label>
                        </div>
                    </div>
                    <input type="text" class="form-control" id="i-link">
                </div>
                <div class="custom-control custom-checkbox customlink-checkbox">
                    <input type="checkbox" class="custom-control-input" id="cb_customlink">
                    <label class="custom-control-label" for="cb_customlink">
                        自定义短链接
                    </label>
                </div>
                <div class="col-sm-4 customlink-input">
                    <input type="text" id="i-customlink" class="form-control">
                </div>
                <div class="custom-control custom-checkbox expires-checkbox">
                    <input type="checkbox" class="custom-control-input" id="cb_expires">
                    <label class="custom-control-label" for="cb_expires">
                        过期时间
                    </label>
                </div>
                <div class="col-sm-4 expires-container">
                    <div class="form-group">
                        <div class="input-group date" id="expires-timepicker" data-target-input="nearest">
                            <input type="text" id="i-expires" class="form-control datetimepicker-input" data-target="#expires-timepicker" />
                            <div class="input-group-append" data-target="#expires-timepicker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
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
        //init
        var api = getCookie('api-choice');
        var use_custom_link = false;
        var use_link_expires = false;

        //初始化
        $('#cb_customlink').prop('checked', false);
        $('#cb_expires').prop('checked', false);

        if (api == null) {
            api = 0;
            $('#r-localsite').prop("checked", true);
        } else if (api == "0") {
            api = 0;
            $('#r-localsite').prop("checked", true);
            if ($('#cb_customlink').attr('disabled') != undefined) {
                $('#cb_customlink').removeAttr('disabled');
            }
            if ($('#i-customlink').attr('disabled') != undefined) {
                $('#i-customlink').removeAttr('disabled');
            }
            if ($('#cb_expires').attr('disabled') != undefined) {
                $('#cb_expires').removeAttr('disabled');
            }
            if ($('#i-expires').attr('disabled') != undefined) {
                $('#i-expires').removeAttr('disabled');
            }
        } else if (api == "1") {
            api = 1;
            $('#r-sinaapp').prop("checked", true);
            $('#cb_customlink').attr('disabled', 'disabled');
            $('#i-customlink').attr('disabled', 'disabled');
            $('#cb_expires').attr('disabled', 'disabled');
            $('#i-expires').attr('disabled', 'disabled');
        }

        //绑定事件
        $('#cb_customlink').change(function () {
            use_custom_link = !use_custom_link;
            if ($('.customlink-input').attr('style') != undefined) {
                if ($(window).width() < 1366) {
                    $('.customlink-checkbox').removeAttr('style');
                }
                $('.customlink-input').removeAttr('style');
            } else {
                if ($(window).width() >= 1366) {
                    $('.customlink-input').attr('style', 'display: inline-block;');
                } else {
                    $('.customlink-checkbox').attr('style', 'display: block');
                    $('.customlink-input').attr('style', 'display: block;');
                }
            }
        });

        $('#cb_expires').change(function () {
            use_link_expires = !use_link_expires;
            if ($('.expires-container').attr('style') != undefined) {
                if ($(window).width() < 1366) {
                    $('.expires-checkbox').removeAttr('style');
                }
                $('.expires-container').removeAttr('style');
            } else {
                if ($(window).width() >= 1366) {
                    $('.expires-container').attr('style', 'display: inline-block;');
                } else {
                    $('.expires-checkbox').attr('style', 'display: block');
                    $('.expires-container').attr('style', 'display: block;');
                }
            }
        });
        $('#r-localsite').click(function () {
            api = 0;
            setCookie('api-choice', api, 30);
            if ($('#cb_customlink').attr('disabled') != undefined) {
                $('#cb_customlink').removeAttr('disabled');
            }
            if ($('#i-customlink').attr('disabled') != undefined) {
                $('#i-customlink').removeAttr('disabled');
            }
            if ($('#cb_expires').attr('disabled') != undefined) {
                $('#cb_expires').removeAttr('disabled');
            }
            if ($('#i-expires').attr('disabled') != undefined) {
                $('#i-expires').removeAttr('disabled');
            }
        });
        $('#r-sinaapp').click(function () {
            api = 1;
            setCookie('api-choice', api, 30);
            $('#cb_customlink').attr('disabled', 'disabled');
            $('#i-customlink').attr('disabled', 'disabled');
            $('#cb_expires').attr('disabled', 'disabled');
            $('#i-expires').attr('disabled', 'disabled');
        });

        $('#expires-timepicker').datetimepicker({
            minDate: moment().startOf('minute')
        });

        var resizeChanged = false;

        $(window).resize(function () {
            //执行代码块
            if ($(window).width() > 1366 && !resizeChanged) {
                resizeChanged = true;
                $('.customlink-checkbox').attr('style', 'display: inline-block;');
                $('.expires-checkbox').attr('style', 'display: inline-block');
                if (use_custom_link) {
                    $('.customlink-input').attr('style', 'display: inline-block;');
                }
                if (use_link_expires) {
                    $('.expires-container').attr('style', 'display: inline-block');
                }
            } else {
                resizeChanged = false;
            }
        });

        //stat
        $.get('/stat.php');
    </script>

    <!-- animation -->
    <?php if (!is_mobile_request()){ ?>
    <script color="28,28,28" opacity='0.7' zIndex="-2" count="120" src="static/canvas-nest.js"></script>
    <?php } ?>
</body>

</html>