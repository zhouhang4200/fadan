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
</head>
<body>
    <div class="main">
        <div class="layui-fluid">
            <div class="layui-row">
                <div class="layui-col-sm6">
                    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                        <ul class="layui-tab-title">
                            <li class="layui-this">登录</li>
                            <li>注册</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <form class="layui-form" action="">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">
                                            <i class="iconfont icon-wode"></i>
                                        </label>
                                        <div class="layui-input-block">
                                            <input type="text" name="title" autocomplete="off" placeholder="请输入账号" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">
                                            <i class="iconfont icon-07"></i>
                                        </label>
                                        <div class="layui-input-block">
                                            <input type="password" name="pwd" autocomplete="off" placeholder="请输入密码" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <img src="/frontend/v1/images/vcode.jpg" alt="" style="height: 40px;margin-top: 10px;">
                                    </div>
                                    <div class="layui-form-item" style="border: 0">
                                        <button class="layui-btn" lay-submit="" lay-filter="login">登录</button>
                                        <a href="#">忘记密码</a>
                                    </div>
                                </form>
                            </div>
                            <div class="layui-tab-item">
                                <form class="layui-form" action="">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">
                                                <i class="iconfont icon-wode"></i>
                                            </label>
                                            <div class="layui-input-block">
                                                <input type="text" name="title" autocomplete="off" placeholder="请输入账号名 (可写中文)" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="title" autocomplete="off" placeholder="请输入昵称" class="layui-input">
                                                </div>
                                        </div>

                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="title" autocomplete="off" placeholder="请输入邮箱 (用于找回密码)" class="layui-input">
                                                </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="title" autocomplete="off" placeholder="请输入QQ" class="layui-input">
                                                </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-wode"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="phone" autocomplete="off" placeholder="请输入手机号" class="layui-input">
                                                </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">
                                                <i class="iconfont icon-07"></i>
                                            </label>
                                            <div class="layui-input-block">
                                                <input type="password" name="pwd" autocomplete="off" placeholder="请输入最少6位数密码" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                                <label class="layui-form-label">
                                                    <i class="iconfont icon-07"></i>
                                                </label>
                                                <div class="layui-input-block">
                                                    <input type="password" name="pwd" autocomplete="off" placeholder="再次输入密码" class="layui-input">
                                                </div>
                                            </div>
                                        <div class="layui-form-item">
                                            <img src="/frontend/v1/images/vcode.jpg" alt="" style="height: 40px;">
                                        </div>
                                        <div class="layui-form-item" style="border: 0">
                                            <button class="layui-btn" lay-submit="" lay-filter="login">注册</button>
                                            <a href="#">去登陆</a>
                                        </div>
                                    </form>
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
        layui.use(['element', 'form'], function () {
            var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
            form = layui.form;
            //监听提交
            form.on('submit(login)', function (data) {
                layer.alert(JSON.stringify(data.field), {
                    title: '最终的提交信息'
                })
                return false;
            });

        })
    </script>
</body>

</html>