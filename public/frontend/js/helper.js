// 获取url 参数
function getQueryString(url, variable) {
    var str = url; //取得整个地址栏
    var num = str.indexOf("?");
    str = str.substr(num + 1); //取得所有参数   stringvar.substr(start [, length ]
    var arr = str.split("&"); //各个参数放到数组里
    for (var i = 0; i < arr.length; i++) {
        var temp = arr[i].split("=");
        if (temp[0] == variable) {
            return temp[1];
        }
    }
}