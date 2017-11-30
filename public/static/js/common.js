// 获取地址栏参数
function get_query_string(name) {
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!==null)return  unescape(r[2]); return null;
}

// toast 支付宝ant ui 已废弃
function toast(str, hide) {
    $(".am-toast").children(".am-toast-text").children(".my_text").text(str).parent().parent().fadeIn("slow");
    if (hide) {
        // toast 3秒后自动消失
        setTimeout("$('.am-toast').fadeOut()", 3000);
        setTimeout("$('.my_text').text('')", 4000);
    }
}

/**
 *
 * @param str 弹出框显示内容
 * @param type info success network fail
 * @param hide 1秒后是否隐藏
 */
function toast_show(str, type, hide) {
    type = type === undefined ? 'warn' : type;
    hide = hide === undefined ? true : hide;
    // 先移除.am-toast
    $('.am-toast').remove();

    var item = "<div class=\"am-toast\" role=\"alert\" aria-live=\"assertive\">\n" +
        "    <div class=\"am-toast-text\">\n" +
        "        <span class=\"am-icon toast "+ type +"\" aria-hidden=\"true\"></span>\n" +
        "        <span class=\"my_text\">"+ str +"</span>\n" +
        "    </div>\n" +
        "</div>";
    $('body').append(item);

    if (hide) {
        // toast 1秒后自动消失
        setTimeout("$('.am-toast').fadeOut()", 1000);
        setTimeout("$('.my_text').text('')", 2000);
    }
}
// 隐藏所有弹出框
function toast_hide() {
    $('.am-toast').remove();
}

/**
 * // 操作localStorage
 * @param k
 * @param v
 * @returns {*}
 * @constructor
 */
function LS(k, v) {
    if (!window.localStorage){
        alert('您的浏览器不支持localStorage');
        return false;
    }else {
        var rs;
        if (v === undefined){
            rs = localStorage.getItem(k);
        }else if(v === null){
            rs = localStorage.removeItem(k);
        }else {
            rs = localStorage.setItem(k,v);
        }

        return rs;
    }
}

/**
 * // 操作sessionStorage
 * @param k
 * @param v
 * @returns {*}
 * @constructor
 */
function SS(k, v) {
    if (!window.sessionStorage){
        alert('您的浏览器不支持sessionStorage');
        return false;
    }else {
        var rs;
        if (v === undefined) {
            rs = sessionStorage.getItem(k);
        }else if(v === null){
            rs = sessionStorage.removeItem(k);
        }else {
            rs = sessionStorage.setItem(k, v);
        }

        return rs;
    }
}

/**
 * // 操作cookie
 * @param k
 * @param v
 * @param e
 * @returns {*}
 * @constructor
 */
function CS(k, v, e) {
    if (navigator.cookieEnabled){
        var rs;
        if (v === undefined) {
            rs = getCookie(k);
        }else if(v === null){
            rs = delCookie(k);
        }else {
            rs = setCookie(k,v,e);
        }

        return rs;
    }else {
        alert('cookie已被禁用');
        return false;
    }
}
function setCookie(c_name,value,expiredays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +escape(value)+
        ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())

    return true;
}
function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1)
        {
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end))
        }
    }

    return false;
}
function delCookie(name)
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null)
        document.cookie= name + "="+cval+";expires="+exp.toGMTString();

    return true;
}

// 封装jQuery Ajax的post操作
function post(url, data, success, async) {
    var md5_password = CS('md5_password');
    data.interface = url;
    var proxy = '../../include/proxy.php';
    if(async === undefined){
        async = true;
    }
    $.ajax({
        url:proxy,
        type:'POST',
        data:data,
        dataType:'json',
        async:async,
        success:function (e) {
            // alert(CS('md5_password'));
            // toast_show(e.msg);
            // alert(SS('token'));
            if(e.code === 40013){
                post(
                    'v2/login',
                    {user_name:SS('user_name'),user_pass:md5_password},
                    function (e2) {
                        if (e2.code === 200){
                            SS('token',e2.token);
                            // 再次发起请求
                            $.ajax({
                                url:proxy,
                                type:'POST',
                                data:data,
                                dataType:'json',
                                async:async,
                                success:function (e) {
                                    if(e.code === 40013){
                                        post(
                                            'v2/login',
                                            {user_name:SS('user_name'),user_pass:md5_password},
                                            function (e2) {
                                                if (e2.code === 200){
                                                    SS('token',e2.token);
                                                    // 再次发起请求
                                                }else {
                                                    alert(e2.msg);
                                                }
                                            }
                                        )
                                    }else {
                                        success(e);
                                    }
                                },
                                error:function (e) {
                                    console.log(e);
                                }
                            })
                        }else {
                            alert(e2.msg);
                        }
                    }
                )
            }else {
                success(e);
            }
        },
        error:function (e) {
            console.log(e);
        }
    })
}