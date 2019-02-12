//cookie
/*
    setCookie(name,value,day)
    name - cookie名称
    value - cookie值
    day - 有效期（天）
*/
function setCookie(name, value, day) {
    var Days = day;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
}

function setCookie_raw(name, value, day) {
    var Days = day;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + value + ";expires=" + exp.toGMTString() + ";path=/";
}

/*
    getCookie(cname)
    cname - cookie名称
    返回字符串
*/
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return null;
}

function animateCSS(element, animationName, speed, callback) {
    var node = document.querySelector(element);
    if (typeof speed == "string"){
        node.classList.add('animated', animationName, speed);
    } else {
        node.classList.add('animated', animationName);
    }

    function handleAnimationEnd() {
        if (typeof speed == "string"){
            node.classList.remove('animated', animationName, speed);
        } else {
            node.classList.add('animated', animationName);
        }
        node.removeEventListener('animationend', handleAnimationEnd);

        if (typeof callback === 'function') callback();
    }

    node.addEventListener('animationend', handleAnimationEnd);
}

function IsURL(str_url){
    var strRegex = "^(http|https|ftp|steam)\:\/\/([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.([a-zA-Z]{2,7}))(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\?\'\\\+&%\$#\=~_\-]+))*$";
    var re=new RegExp(strRegex);
    if (re.test(str_url)){
        return true;
    }else{
        return false;
    }
}

function checkSubmit(){
    var link = $('#i-link').val().trim();
    var custom_link = $('#i-custom-link').val().trim();
    if (IsURL(link)){
        if (use_custom_link){
            if (custom_link.length > 0){
                var strReg = '^[A-Za-z0-9\u4e00-\u9fa5]+$';
                var re = new RegExp(strReg);
                var res = re.exec(custom_link);
                if (res != custom_link || res == null){
                    $('#i-custom-link').addClass('is-invalid');
                    toastr.error("自定义链接中不能包含特殊字符。");
                } else {
                    requestShortLink(custom_link, custom_link);
                }
            } else {
                $('#i-custom-link').addClass('is-invalid');
                toastr.error('请输入自定义链接。');
            }
        } else {
            requestShortLink(link);
        }
    } else {
        $('#i-link').addClass('is-invalid');
        toastr.error('输入的不是合法URL，请重试。');
    }
}

function requestShortLink(link, custom_link){
    if ($('#i-link').hasClass('is-invalid')){
        $('#i-link').removeClass('is-invalid');
    }
    if ($('#i-custom-link').hasClass('is-invalid')){
        $('#i-custom-link').removeClass('is-invalid');
    }
    if (api == 0){
        $('#btn-generate').attr('disabled','disabled');
        $.ajax({
            url: '/index.php',
            type: 'POST',
            data: {
                action: 'shortlink',
                
            }
        });
    } else if (api == 1){
        $('#btn-generate').attr('disabled','disabled');
        $.ajax({
            url: 'http://api.t.sina.com.cn/short_url/shorten.json',
            type: 'GET',
            data:{
                source: 3271760578,
                url_long: link
            },
            dataType: 'json',
            success: function(data){
                $('#btn-generate').removeAttr('disabled','disabled');
                if (data.error_code != undefined){
                    if (data.error_code == 400){
                        toastr.error('输入的URL不合法，请重试。');
                    }
                } else {
                    if ($('.row-generated').attr('style') == undefined){
                        $('.row-generated').attr('style','display:block');
                        animateCSS('.row-generated', 'fadeIn', 'faster');
                    }
                    $('#link-card-body').html('<a href=\''+ data.url_short +'\'>'+ data.url_short +'</a>');
                }
            },
            error: function(e){
                console.error(e);
                $('#btn-generate').removeAttr('disabled','disabled');
                toastr.error('无法提交请求。');
            }
        });
    }
}