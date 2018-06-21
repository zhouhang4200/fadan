
/**
 * JS表单校验静态工具类
 * 
 * @author 程伟平 2012-4-12
 */
function CheckUtil() {
	
}

/**
 * 删除字符串前导空白和尾部空白
 * 
 * @author 程伟平 2012-4-12
 */
CheckUtil.trim = function(str) {
	return (str || "").replace(/(^[\s]*)|([\s]*$)/g, "");
};

/**
 * 删除字符串前导空白
 * 
 * @author 程伟平 2012-4-12
 */
CheckUtil.leftTrim = function(str) {
	return (str || "").replace(/(^[\s]*)/g, "");
};

/**
 * 删除字符串尾部空白
 * 
 * @author 程伟平 2012-4-12
 */
CheckUtil.rightTrim = function(str) {
	return (str || "").replace(/([\s]*$)/g, "");
};

/**
 * 校验是否为非空
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isNotNull = function (str) {
	return null != str && CheckUtil.trim(str).length > 0 ? true : false;
};

/**
 * 校验是否全是数字
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isNumeric  = function (str) {
	var pattern=/^\d+$/;
	return pattern.test(str);
};

/**
 * 校验是否是整数
 * 
 * @author 程伟平 2012-4-12
 * @param str 字符串
 * @param symbol 符号 说明："+"表示正整数；"-"表示负整数。
 * @return 是：true； 否：false；
 */
CheckUtil.isInt = function (str, symbol) {
	// 正整数
	if(symbol === "+") {
		var pattern=/^([+]?)(\d+)$/;
		return pattern.test(str);
	}
	// 负整数
	else if(symbol === "-") {
		var pattern=/^-(\d+)$/;
		return pattern.test(str);
	} 
	// 整数
	else {
		var pattern=/^([+-]?)(\d+)$/;
		return pattern.test(str);
	}
};

/**
 * 校验是否为浮点数
 * 
 * @author 程伟平 2012-4-12
 * @param str 字符串
 * @param symbol 符号 说明："+"表示正整数；"-"表示负整数。
 * @return 是：true； 否：false；
 */
CheckUtil.isFloat=function(str, symbol) {
	// 正浮点数
	if(symbol === "+") {
		var pattern=/^([+]?)\d*\.\d+$/;
		return pattern.test(str);
	}
	// 负浮点数
	else if(symbol === "-") {
		var pattern=/^-\d*\.\d+$/;
		return pattern.test(str);
	} 
	// 浮点数
	else {
		var pattern=/^([+-]?)\d*\.\d+$/;
		return pattern.test(str);
	}
};

/**
 * 校验是否仅中文
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isChinese=function(str){
	var pattern=/[\u4E00-\u9FA5\uF900-\uFA2D]+$/;
	return pattern.test(str);
};
   /** 
    * 获取字符真实的长度
    * 如果包含中文 算 2个字符 
    */
 CheckUtil.trueLength= function(s) {
	  var l = 0;
	  var a = s.split("");
	  for (var i=0;i<a.length;i++) {
	     if (CheckUtil.isChinese(a[i])) {
	        l+=2;
	     } else {
	        l++;
	     }
	  }
	  return l;
    }
/**
 * 校验是否仅ACSII字符
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isACSII=function(str){
	var pattern=/^[\x00-\xFF]+$/;
	return pattern.test(str);
};
/**
 * 校验是否仅 拼音
 * 
 * @author ouyang 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isACSII=function(str){
	var pattern=/^[a-zA-Z]+$/;
	return pattern.test(str);
};

/**
 * 校验手机号码
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isMobile = function (str) {
	var pattern = /^1[345678]\d{9}$/;
	return pattern.test(str);
};
/**
 * 校验是否为Q号码
 */
CheckUtil.isQQ = function (str) {
	var pattern = /^(\d){4,15}$/;
	return pattern.test(str);
};
/**
 * 校验是否电话号码
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isPhone = function (str) {
	var pattern = /^(0[\d]{2,3}-)?\d{6,8}(-\d{3,4})?$/;
	return pattern.test(str);
};

/**
 * 校验是否URL地址
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isUrl=function(str_url){ 
       var strRegex = "^((https|http|ftp|rtsp|mms)?://)"  
       + "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" //ftp的user@  
       + "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP形式的URL- 199.194.52.184  
       + "|" // 允许IP和DOMAIN（域名） 
       + "([0-9a-z_!~*'()-]+\.)*" // 域名- www.  
       + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // 二级域名  
       + "[a-z]{2,6})" // first level domain- .com or .museum  
       + "(:[0-9]{1,4})?" // 端口- :80  
        + "((/?)|" // a slash isn't required if there is no file name  
        + "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";  
       var re=new RegExp(strRegex);  
        //re.test() 
       if (re.test(str_url)){ 
           return (true);  
       }else{  
           return (false);  
       } 
    } 

/**
 * 校验是否电子邮件地址
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isEmail = function (str) {
	var pattern = /^([a-zA-Z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
	return pattern.test(str);
};

/**
 * 校验是否全是字母
 * 
 * @author 张春艳 2013-10-20
 * @return 是：true； 否：false；
 */
CheckUtil.isLetter  = function (str) {
	var pattern2=/^[A-Za-z]+$/;
	return pattern2.test(str);
};

/**
 * 校验是否身份证
 * 
 */
CheckUtil.isIdcard = function(id) {
 var idNum = id.toLocaleUpperCase();
 var errors = new Array(
  "验证通过",
  "身份证号码位数不对",
   "身份证含有非法字符",
  "身份证号码校验错误",
  "身份证地区非法"
 );
 //身份号码位数及格式检验
 var re;
 var len = idNum.length;
 //身份证位数检验
 if (len != 15 && len != 18) {
     //return errors[1];
     return false;
 } else if (len == 15) {
     re = new RegExp(/^(\d{6})()?(\d{2})(\d{2})(\d{2})(\d{3})$/);
 } else {
     re = new RegExp(/^(\d{6})()?(\d{4})(\d{2})(\d{2})(\d{3})([0-9xX])$/);
 }
 var area = { 11: "北京", 12: "天津", 13: "河北", 14: "山西",
     15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海",
     32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西",
     37: "山东", 41: "河南", 42: "湖北", 43: "湖南", 44: "广东",
     45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州",
     53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃", 63: "青海",
     64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门",
     91: "国外"
 }
 var idcard_array = new Array();
 idcard_array = idNum.split("");
 //地区检验
 if (area[parseInt(idNum.substr(0, 2))] == null) {
     return false;
     //return errors[4];
 }
 //出生日期正确性检验
 var a = idNum.match(re);
 if (a != null) {
     if (len == 15) {
         var DD = new Date("19" + a[3] + "/" + a[4] + "/" + a[5]);
         var flag = DD.getYear() == a[3] && (DD.getMonth() + 1) == a[4] && DD.getDate() == a[5];
     }
     else if (len == 18) {
         var DD = new Date(a[3] + "/" + a[4] + "/" + a[5]);
         var flag = DD.getFullYear() == a[3] && (DD.getMonth() + 1) == a[4] && DD.getDate() == a[5];
     }
     if (!flag) {
         return false;
         //return "身份证出生日期不对！"; 
     }
     //检验校验位
     if (len == 18) {
         S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7
            + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9
            + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10
            + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5
            + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8
            + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4
            + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2
            + parseInt(idcard_array[7]) * 1
            + parseInt(idcard_array[8]) * 6
            + parseInt(idcard_array[9]) * 3;
         Y = S % 11;
         M = "F";
         JYM = "10X98765432";
         M = JYM.substr(Y, 1); //判断校验位
         //检测ID的校验位
         if (M == idcard_array[17]) {
             return true;
             //return ""; 
         }
         else {
             return false;
             //return errors[3];
         }
     }
 } else {
     return false;
     //return errors[2];
 }
 return true;
}
/**
 * 校验是否邮政编码
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isZipCode = function (str) {
	var pattern = /^\d{6}$/;
	return pattern.test(str);
};

/**
 * 校验是否时间
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isDate = function (str) {
	if(!/\d{4}(\.|\/|\-)\d{1,2}(\.|\/|\-)\d{1,2}/.test(str)){return false;}
	var r = str.match(/\d{1,4}/g);
	if(r==null){return false;};
	var d= new Date(r[0], r[1]-1, r[2]);
	return (d.getFullYear()==r[0]&&(d.getMonth()+1)==r[1]&&d.getDate()==r[2]);
};

/**
 * 校验字符串：由6-20位字母、数字、下划线(常用于校验用户名)
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isFixedString6_20 = function(str){
	var pattern=/^(\w){6,20}$/;
	return pattern.test(str);
};

/**
 * 校验字符串——由5-20位字母、数字、下划线(常用于校验用户名)
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isFixedString5_20 = function(str){
	var pattern=/^(\w){5,20}$/;
	return pattern.test(str);
};

/**
 * 校验密码字符串：由6-20位数字、字母和特殊符号组成。
 * 
 * @author 程伟平 2012-4-12
 * @return 是：true； 否：false；
 */
CheckUtil.isPassword = function(str){
	var pattern=/^[\w\W]{6,20}$/;
	return pattern.test(str);
};

/**
 * 校验密码强度 说明：1：弱；2：中；3：强；
 * 
 * @author 程伟平 2012-5-15
 * @return 1：弱；2：中；3：强；
 */
CheckUtil.passwordStrength = function(password){
	if (password.length < 6) {
		return 1;
	}
	return password.match(/[a-z](?![^a-z]*[a-z])|[A-Z](?![^A-Z]*[A-Z])|\d(?![^\d]*\d)|[^a-zA-Z\d](?![a-zA-Z\d]*[^a-zA-Z\d])/g).length;
};
function SelfTextCut(str,bs,startCount,endCount){
	bs = "******";
	str = str.replace(/\n|\r/g,'');
	return str.substring(0,startCount)+ bs + str.substring(str.length-endCount,str.length)
};
function  SelfTextCut2(str, count,bs) {
	bs = bs || "......";
	str = str.replace(/\n|\r/g,'');
    if (str.length > count) {
        return str.substring(0, count / 2) + bs + str.substring(str.length - (count / 2), str.length)
    };
    return str;
};
function  TextCut(str, count) {
	str = str.replace(/\n|\r/g,'');
    if (str.length > count) {
        return str.substring(0, count / 2) + "......" + str.substring(str.length - (count / 2), str.length)
    };
    return str;
};
function  TextCutEnd(str, count) {
	str = str.replace(/\n|\r/g,'');
	
    if (CheckUtil.trueLength(str) > parseInt(count)*2 ) {
        return str.substring(0, count-3) + "...";
    };
    return str;
};
function TextCount(str) {
    var len = str.length;
    var reLen = 0;
    for (var i = 0; i < len; i++) {
        if (/^[\u4e00-\u9fa5]{0,}$/.test(str.substring(i, i + 1))) {
            reLen += 2;
        } else {
            if (reLen == 0) {
                reLen++;
            }
            reLen++;
        }
    }
    return reLen;
};
$(function(){
//首先将#indexToTop隐藏
	$(".w-button-backToTop").hide();
	//当滚动条的位置处于距顶部100像素以下时，跳转链接出现，否则消失
	$(function () {
		$(window).scroll(function(){
		if ($(window).scrollTop()>150){
		$(".w-button-backToTop").fadeIn(500);
		$(".nrefresh").css("bottom","125px");
		}
		else
			
		{
		$(".w-button-backToTop").fadeOut(500);
		$(".nrefresh").css("bottom","65px");
		}
		});
		//当点击跳转链接后，回到页面顶部位置
		$(".w-button-backToTop").click(function(){
		$('body,html').animate({scrollTop:0},1000);
		return false;
		});
		});
});