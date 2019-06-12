//copyright
console.log('%cShortLink System%c\nDeveloped by BackRunner from pwp.app\nAn open source application under MIT license.\nversion: 0.9.8','font-size:20px;color:#1faeff;','font-size: 12px;color:#8f8f8f;');

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
    var strRegex = "(https?|ftp|file|steam)://[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|]";
    var re=new RegExp(strRegex);
    if (re.test(str_url)){
        return true;
    }else{
        return false;
    }
}

function checkSubmit(){
    var link = $('#i-link').val();
    var custom_link = $('#i-customlink').val();
    var expires = $('#i-expires').val();
    var now = moment().unix();

    var form_checked = true;

    if (typeof link != 'undefined'){
        link = link.trim();
    }
    if (typeof custom_link != 'undefined'){
        custom_link = custom_link.trim();
    }
    if (typeof expires != 'undefined'){
        expires = expires.trim();
    }
    //表单检查
    if (!IsURL(link)){
        form_checked = false;
        $('#i-link').addClass('is-invalid');
        toastr.error('输入的不是合法URL，请重试。');
    } else {
        if ($('#i-link').hasClass('is-invalid')){
            $('#i-link').removeClass('is-invalid');
        }
    }
    if (api == 0){
        if (use_custom_link){
            if (typeof custom_link != 'undefined'){
                if (custom_link.length > 0){
                    custom_link = custom_link.trim();
                    var strReg = '^[A-Za-z0-9\u4e00-\u9fa5]+$';
                    var re = new RegExp(strReg);
                    var res = re.exec(custom_link);
                    if (res != custom_link || res == null){
                        form_checked = false;
                        $('#i-customlink').addClass('is-invalid');
                        toastr.error("自定义链接中不能包含特殊字符。");
                    }
                    var reserved = ['manage','install','static'];
                    if (reserved.indexOf(custom_link)>=0){
                        form_checked = false;
                        $('#i-customlink').addClass('is-invalid');
                        toastr.error("请勿使用保留字作为自定义短链接。");
                    }
                } else {
                    form_checked = false;
                    $('#i-customlink').addClass('is-invalid');
                    toastr.error('请输入自定义链接。');
                }
            } else {
                form_checked = false;
                $('#i-customlink').addClass('is-invalid');
                toastr.error('请输入自定义链接。');
            }
        } else {
            custom_link = undefined;
        }
        if (use_link_expires){
            if (typeof expires != 'undefined'){
                if (expires.length > 0){
                    try{
                        expires = moment(expires,'YYYY-MM-DD HH:mm').unix();
                        if (expires <= now){
                            form_checked = false;
                            $('#i-expires').addClass('is-invalid');
                            toastr.error('请输入合法的时间。');
                        }
                    } catch (err){
                        form_checked = false;
                        $('#i-expires').addClass('is-invalid');
                        toastr.error('请输入合法的时间。');
                    }
                } else {
                    form_checked = false;
                    $('#i-expires').addClass('is-invalid');
                    toastr.error('请选择过期时间。');
                }
            } else {
                form_checked = false;
                $('#i-expires').addClass('is-invalid');
                toastr.error('请选择过期时间。');
            }
        } else {
            expires = undefined;
        }
    }
    if (form_checked){
        requestShortLink(link, custom_link, expires);
    }
}

function checkExpires(){
    if (use_link_expires){
        if (expires.length > 0){
            var expires_unix = moment(expires).unix();
            console.log(expires_unix);
            return true;
        } else {
            $('#i-expires').addClass('is-invalid');
            toastr.error('请选择过期时间。');
        }
    }
}

function str10to62(number) {
    var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'.split(''),
      radix = chars.length,
      qutient = +number,
      arr = [];
    do {
      mod = qutient % radix;
      qutient = (qutient - mod) / radix;
      arr.unshift(chars[mod]);
    } while (qutient);
    return arr.join('');
}

function requestShortLink(link, custom_link, expires){
    if ($('#i-link').hasClass('is-invalid')){
        $('#i-link').removeClass('is-invalid');
    }
    if ($('#i-customlink').hasClass('is-invalid')){
        $('#i-customlink').removeClass('is-invalid');
    }
    if ($('#i-expires').hasClass('is-invalid')){
        $('#i-expires').removeClass('is-invalid');
    }
    if (api == 0){
        $('#btn-generate').attr('disabled','disabled');
        $.ajax({
            url: '/index.php',
            type: 'GET',
            data: {
                action: 'shortlink',
                link_type: use_custom_link?'custom':'normal',
                long_link: link,
                custom_link: custom_link,
                expires: expires
            },
            dataType: 'json',
            success: function(data){
                $('#btn-generate').removeAttr('disabled','disabled');
                if (data.error_code == undefined){
                    if ($('.row-generated').attr('style') == undefined){
                        $('.row-generated').attr('style','display:block');
                        animateCSS('.row-generated', 'fadeIn', 'faster');
                    }
                    if (use_custom_link){
                        $('#link-card-body').html('<a href=\''+ window.location.origin+'/'+custom_link +'\'>'+ window.location.origin+'/'+custom_link +'</a>');
                    } else {
                        $('#link-card-body').html('<a href=\''+ window.location.origin+'/'+str10to62(data.short_link)+'\'>'+ window.location.origin+'/'+str10to62(data.short_link)+'</a>');
                    }
                    toastr.success('短链接生成成功。');
                } else {
                    toastr.error(data.error);
                }
            },
            error: function(e){
                console.error('%cShortLink System - Error', 'font-size:15px;color:red;');
                console.error(e);
                $('#btn-generate').removeAttr('disabled','disabled');
                toastr.error('无法提交请求。');
            }
        });
    } else if (api == 1){
        $('#btn-generate').attr('disabled','disabled');
        $.ajax({
            url: 'https://cors-anywhere.herokuapp.com/https://api.t.sina.com.cn/short_url/shorten.json',
            type: 'GET',
            data:{
                source: 3271760578,
                url_long: link
            },
            dataType: 'json',
            success: function(data){
                $('#btn-generate').removeAttr('disabled','disabled');
                if ($('.row-generated').attr('style') == undefined){
                    $('.row-generated').attr('style','display:block');
                    animateCSS('.row-generated', 'fadeIn', 'faster');
                }
                $('#link-card-body').html('<a href=\''+ data[0].url_short +'\'>'+ data[0].url_short +'</a>');
                toastr.success('短链接生成成功。');
            },
            error: function(e){
                console.error('%cShortLink System - Error', 'font-size:15px;color:red;');
                console.error(e);
                if (typeof e.responseJSON != 'undefined'){
                    if (e.responseJSON.error_code != 400){
                        $('#btn-generate').removeAttr('disabled','disabled');
                        toastr.error('无法提交请求。');
                    } else {
                        $('#btn-generate').removeAttr('disabled','disabled');
                        toastr.error('提交的URL不合法。');
                    }
                } else {
                    $('#btn-generate').removeAttr('disabled','disabled');
                    toastr.error('无法提交请求。');
                }
            }
        });
    }
}