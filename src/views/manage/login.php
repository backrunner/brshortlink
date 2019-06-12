<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        登录 - <?php echo SITE_NAME; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../static/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/main.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/toastr.min.css" />
    <link rel="stylesheet" type="text/css" href="../static/font-awesome.min.css" />
    <script src="../static/jquery.min.js"></script>
    <script src="../static/admin.function.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <script src="../static/toastr.min.js"></script>
    <script src="../static/crypto-js.js"></script>
</head>

<body class="manage-body">
    <div class="container manage-container" id="main-container">
        <div class="row">
            <div class="col-lg-12 page-header">
                <h2>B<mhide>ack</mhide>R<mhide>unner</mhide>'s ShortLink</h2>
                <label class="page-header-label">管理</label>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 header">
                <h3>登录</h3>
            </div>
            <div class="col-lg-12">
                <div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>用户名</label></div>
                        <div class="col-sm-10"><input type="text" id="i-username" class="form-control" maxlength="30"></div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-2"><label>密码</label></div>
                        <div class="col-sm-10"><input type="password" id="i-password" class="form-control"></div>
                    </div>
                    <mhide>
                        <div class="form-row">
                            <div class="col-sm-10">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="cb_autologin">
                                    <label class="custom-control-label" for="cb_autologin" style="line-height:initial !important;">
                                        7天内自动登录
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-2"><button class="btn btn-block btn-primary btn-manage-login" id="btn-login" onclick="checkForm();">登录</button></div>
                        </div>
                    </mhide>
                    <mvisi>
                        <div class="form-row">
                            <div class="col-sm-10">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="cb_autologin_mobile">
                                    <label class="custom-control-label" for="cb_autologin_mobile" style="line-height:initial !important;">
                                        7天内自动登录
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-2"><label>登录</label></div>
                            <div class="col-sm-10"><button class="btn btn-block btn-primary btn-manage-login" id="btn-login-m" onclick="checkForm();">登录</button></div>
                        </div>
                    </mvisi>
                </div>
            </div>
        </div>
    </div>
    <script>
        //键盘事件绑定
        $('#i-username').keydown(function (e) {
            if (e.keyCode == 13) {
                checkForm();
            }
        });
        $('#i-password').keydown(function (e) {
            if (e.keyCode == 13) {
                checkForm();
            }
        });

        function checkForm() {
            var username = $("#i-username").val();
            var password = $("#i-password").val();

            var isFormChecked = true;

            var username_fail = false;
            var password_fail = false;

            if (username.length < 1) {
                $('#i-username').addClass('is-invalid');
                toastr.error('请输入用户名。');
                isFormChecked = false;
                username_fail = true;
            }
            if (password.length < 1) {
                $('#i-password').addClass('is-invalid');
                toastr.error('请输入密码。');
                isFormChecked = false;
                password_fail = true;
            }

            //特殊字符检测
            var strReg = '^([A-Za-z0-9!@#._])+$';
            var re = new RegExp(strReg);

            if (!username_fail) {
                var res = re.exec(username);
                if (res == null) {
                    isFormChecked = false;
                    $('#i-username').addClass('is-invalid');
                    toastr.error("用户名不允许包含中文、特殊字符。");
                }
            }

            if (!password_fail) {
                res = re.exec(password);
                if (res == null) {
                    isFormChecked = false;
                    $('#i-password').addClass('is-invalid');
                    toastr.error("密码不允许包含中文、特殊字符。");
                }
            }

            if (isFormChecked) {
                submitLogin(username, password);
            }
        }

        function submitLogin(username, password) {
            let p = CryptoJS.SHA256(password).toString();
            console.log(p);
            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {
                    action: 'login',
                    username: username,
                    password: p
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code != 200) {
                        toastr.error(data.error);
                    } else {
                        if (autologin) {
                            setCookie_raw('autologin', username + '|' + p, 7);
                        }
                        window.location.href = "/manage/?p=data";
                    }
                },
                error: function (err) {
                    console.error(err);
                }
            });
        }

        (function(){
            var c = getCookie('autologin')
            if (c != null) {
                c = c.split('|');
                $.ajax({
                    type: 'POST',
                    url: 'action.php',
                    data: {
                        action: 'login',
                        username: c[0],
                        password: c[1]
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.code == 200) {
                            window.location.href = "/manage/?p=data";
                        }
                    },
                    error: function (err) {
                        console.error(err);
                    }
                });
            }
        })();

        var autologin = false;

        $('#cb_autologin').change(function () {
            autologin = !autologin;
            $('#cb_autologin_mobile').prop('checked',autologin);
        });
        $('#cb_autologin_mobile').change(function () {
            autologin = !autologin;
            $('#cb_autologin').prop('checked', autologin);
        });

        if (getCookie('autologin_checked')!=null){
            autologin = true;
            $('#cb_autologin').prop('checked',true);
            $('#cb_autologin_mobile').prop('checked',true);
            var t = getCookie('autologin_username');
            if (t!=null)
                $('#i-username').val(t);
        }
    </script>
    <?php if (!is_mobile_request()){ ?>
    <script color="28,28,28" opacity='0.7' zIndex="-2" count="120" src="../static/canvas-nest.js"></script>
    <?php } ?>
</body>