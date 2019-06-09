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

function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}