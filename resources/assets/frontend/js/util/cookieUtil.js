//获取cookie、
export function getCookie(name) {
    let arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
    if (arr = document.cookie.match(reg))
        return (arr[2]);
    else
        return null;

}

//设置cookie,增加到vue实例方便全局调用
export function setCookie (name, value, expiredays) {
    let exp = new Date();
    exp.setTime(exp.getTime() + 9999 * 24 * 60 * 60 * 1000); //3天过期
    document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString()+";path=/";
    return true;
}

//删除cookie
export function delCookie (name) {
    let exp = new Date();
    exp.setTime(exp.getTime() - 1);
    let cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}