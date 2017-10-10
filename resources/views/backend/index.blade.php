<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>商家后台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="{{ csrf_token() }}" >
    <link rel="stylesheet" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" href="/frontend/css/style.css">

</head>
<body>
<!--START 顶部菜单-->
<div class="header">
    <div class="wrapper">
        <a href="" class="logo">
            <div class="t">
                <h1>千手 · 订单集市</h1>
            </div>
            <div class="en"><img src="/frontend/images/en.png"></div>
        </a>
        <div class="nav">
            <ul>
                <li><a href="">首页</a><div class="arrow"></div></li>
                <li class="current"><a href="">商品</a><div class="arrow"></div></li>
                <li class=""><a href="">财务</a><div class="arrow"></div></li>
                <li class=""><a href="">权限</a><div class="arrow"></div></li>
                <li class=""><a href="">工作台</a><div class="arrow"></div></li>
            </ul>
        </div>
        <div class="user">
            <div class="operation">
                <a href=""><i class="iconfont icon-shezhi"></i>设置</a>
                <a href="javascript:void(0)" onclick="logout()"><i class="iconfont icon-tuichu" style="font-size: 21px;top:1px"></i>注销登录</a>
            </div>
        </div>
    </div>
</div>
<!--END 顶部菜单-->

<!--START 主体-->
<div class="main">
    <div class="wrapper">
        <div class="left">
            <div class="column-menu">
                <ul class="seller_center_left_menu">
                    <li class="current"><a href=""> 商品列表 </a><div class="arrow"></div></li>
                    <li><a href=""> 商品分类 </a><div class="arrow"></div></li>
                </ul>
            </div>
        </div>

        <div class="right">
            <div class="content">

                <div class="path"><span> 商品列表</span></div>

                <div class="layui-tab">
                    <ul class="layui-tab-title">
                        <li class="layui-this">网站设置</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show" lay-size="sm">
                            <table class="layui-table">
                                <colgroup>
                                    <col width="150">
                                    <col width="200">
                                    <col>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>昵称</th>
                                    <th>加入时间</th>
                                    <th>签名</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>贤心</td>
                                    <td>2016-11-29</td>
                                    <td>人生就像是一场修行</td>
                                </tr>
                                <tr>
                                    <td>许闲心</td>
                                    <td>2016-11-28</td>
                                    <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--END 主体-->

<!--START 底部-->
<div class="footer">
    <p>©&nbsp;2017-2018  福禄络科技有限公司，并保留所有权利。Powered by <span class="vol">s.dai.dev</span></p>
</div>
<!--END 底部-->

<script src="/vendor/layui/layui.js"></script>
<script src="/js/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    });

   function logout() {    
        $.post("{{ route('admin.logout') }}", function (data) {
            top.location='/admin/login'; 
        });
    };
        
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;
    });


</script>
</body>
</html>