function checknamelength(){
        var username = document.getElementById("username");//用户名
           if(username.value.length<=3||username.value.length>=8) {   
                 $(".prompt").html("用户名长度必须大于3小于8 !")
           }
           if(username.value == ""){
                    $(".prompt").html("用户名不能为空 !")
                }
            }
function checkpasswordlength(){
        var password = document.getElementById("password");//密码
            if(password.value.length<=6||password.value.length>=12)
                {
                $(".prompt").html("密码长度必须大于6小于12 ！")
                }
               if(password.value == ""){
                    $(".prompt").html("密码不能为空 ！")
                }
        } 
function checkverificationCode(){
            var verificationCode =  document.getElementById('verificationCode');
            if(verificationCode.value == ""){
                $('.prompt').html("验证码不能为空 ！")
            }
            
        }   