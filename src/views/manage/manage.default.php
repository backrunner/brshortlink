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
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2"><button class="btn btn-block btn-primary btn-manage-login" id="btn-login" onclick="checkForm();">登录</button></div>
                        </div>
                    </mhide>
                    <mvisi>
                        <div class="form-row">
                            <div class="col-sm-2"><label>登录</label></div>
                            <div class="col-sm-10"><button class="btn btn-block btn-primary btn-manage-login" id="btn-login-m" onclick="checkForm();">登录</button></div>
                        </div>
                    </mvisi>
                </div>
            </div>
        </div>
    </div>
    <script>
        function checkForm(){
            var username = $("#i-username").val();
            var password = $("#i-password").val();

            var isFormChecked = true;

            if (username.length < 1){
                $('#i-username').addClass('is-invalid');
                toastr.error('请输入用户名。');
                isFormChecked = false;
            }
            if (password.length < 1){
                $('#i-password').addClass('is-invalid');
                toastr.error('请输入密码。');
                isFormChecked = false;
            }

            //特殊字符检测
            var strReg = '^[A-Za-z0-9\u4e00-\u9fa5]+$';
            var re = new RegExp(strReg);
            var res = re.exec(username);

            if (res != username || res == null){
                isFormChecked = false;
                $('#i-username').addClass('is-invalid');
                toastr.error("用户名中不能包含特殊字符。");
            }

            res = re.exec(password);

            if (res != password || res == null){
                isFormChecked = false;
                $('#i-password').addClass('is-invalid');
                toastr.error("密码中不能包含特殊字符。");
            }

            if (isFormChecked){
                submitLogin(username, password);
            }
        }

        function submitLogin(username, password){

        }
    </script>
</body>