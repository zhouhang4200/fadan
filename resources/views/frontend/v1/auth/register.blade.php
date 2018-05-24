<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/frontend/v1/lib/js/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/v1/lib/css/iconfont.css">
    <link rel="stylesheet" href="/frontend/v1/lib/css/new.css">
    <link rel="stylesheet" href="/frontend/v1/lib/css/index.css">
    <title>注册</title>
    <style>
        .geetest_holder.geetest_wind .geetest_btn {
            width:100% !important;
        }
        .geetest_holder {
            width: 100% !important;
        }
        .geetest_holder.geetest_wind {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="layui-fluid">
            <div class="layui-row">
                <div class="layui-col-sm6">
                    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                        <ul class="layui-tab-title">
                            <li><a class="register" href="{{ route('login') }}">登录</a></li>
                            <li class="layui-this">注册</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <!-- <form class="layui-form" action="">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">
                                            <i class="iconfont icon-wode"></i>
                                        </label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号" value="{{ old('name') }}" autocomplete="off" class="layui-input layui-form-danger">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">
                                            <i class="iconfont icon-07"></i>
                                        </label>
                                        <div class="layui-input-block">
                                            <input type="password" name="password" required="" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input layui-form-danger">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        {!! Geetest::render() !!}
                                    </div>
                                    <div class="layui-form-item" style="border: 0">
                                        <button class="layui-btn" lay-submit="" lay-filter="login">登录</button>
                                        <a class="forget-password" href="{{ route('password.request') }}">忘记密码？</a>
                                    </div>
                                </form> -->
                                <!-- <div class="layui-tab-item"> -->
                                    <form class="layui-form" action="">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">
                                                <i class="iconfont icon-wode"></i>
                                            </label>
                                            <div class="layui-input-block">
                                                <input type="text" name="name" required="" lay-verify="required" placeholder="请输入账号名 (可写中文)" value="{{ old('name') }}" autocomplete="off" class="layui-input layui-form-danger">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                     <input type="text" name="username" required="" lay-verify="required" placeholder="请输入昵称" value="{{ old('username') }}" autocomplete="off" class="layui-input layui-form-danger">
                                                </div>
                                        </div>

                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="email" name="email" required="" lay-verify="required|email" placeholder="请输入邮箱 (用于找回密码)" value="{{ old('email') }}" autocomplete="off" class="layui-input layui-form-danger">
                                                </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="qq" required="" lay-verify="required|number" placeholder="请输入QQ" value="{{ old('qq') }}" autocomplete="off" class="layui-input layui-form-danger">
                                                </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="phone" required="" lay-verify="required|phone" placeholder="请输入手机号" value="{{ old('phone') }}" autocomplete="off" class="layui-input layui-form-danger">
                                                </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">
                                                <i class="iconfont icon-07"></i>
                                            </label>
                                            <div class="layui-input-block">
                                                <input type="password" name="password" required="" lay-verify="required" placeholder="请输入最少6位数密码" autocomplete="off" class="layui-input layui-form-danger">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-07"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="password" name="password_confirmation" required="" lay-verify="required" placeholder="再次输入密码" autocomplete="off" class="layui-input layui-form-danger">
                                                </div>
                                            </div>
                                        <div class="layui-form-item">
                                            {!! Geetest::render() !!}
                                        </div>
                                        <div class="layui-form-item" style="border: 0">
                                            <button class="layui-btn" lay-submit="" lay-filter="register">注册</button>
                                            <a class="register" href="{{ route('login') }}">登录</a>
                                        </div>
                                    </form>
                                <!-- </div> -->
                            </div>

                        </div>
                    </div>
                </div>
                <div class="layui-col-sm6">
                    <img src="/frontend/v1/images/right-login.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
    <script src="/frontend/v1/lib/js/layui/layui.js"></script>
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/encrypt.js"></script>
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});

        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form
            ,layer = layui.layer;

            form.on('submit(register)', function (data) {
                $.post('{{ route('register') }}', {
                    name:data.field.name,
                    password:encrypt(data.field.password),
                    password_confirmation:encrypt(data.field.password_confirmation),
                    email:data.field.email,
                    username:data.field.username,
                    qq:data.field.qq,
                    phone:data.field.phone,
                    geetest_challenge:data.field.geetest_challenge,
                    geetest_seccode:data.field.geetest_seccode,
                    geetest_validate:data.field.geetest_validate
                }, function (result) {
                    if (result.status == 1) {
                        location.reload();
                    } else {
                        layer.msg(result.message);
                    }
                    return false;
                });
                return false;
            });
            $('body').height($(window).height());
        });
    </script>
</body>

</html>