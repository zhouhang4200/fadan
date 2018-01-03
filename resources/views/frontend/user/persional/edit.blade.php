@extends('frontend.layouts.app')

@section('title', '首页 - 个人 - 资料修改')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <link href="/frontend/css/iconfont.css" rel="stylesheet">
    <style>
        .layui-table tr th , td {
            text-align: center;
        } 
        .postion{
            position: relative;
        }
        .tip,
        .tips{
            width: 300px;
            height: 50px;
            padding: 5%;
            color: #fff;
            border-radius: 10px;
            background-color:#91C5FF;
            position: absolute;
            left:273px;
            top: -67px;
            padding: 5px;
        }
        .tip{
            top:-65px;
            left: 275px;
        }
        .tip::after,
        .tips::after{
            content: '';
            border: 10px solid rgba(0, 0, 0, 0);
            border-top-color:#91C5FF; 
            position: absolute;
            right: 255px;
            top:60px;
        }
        .none{
            display: none;
        }
        #recharge,
        #store{
            font-size: 20px;
            position: absolute;
            left: 310px;
            top: 7px;
        }

    </style>
@endsection

@section('submenu')
    <ul class="seller_center_left_menu">
        <li class="{{ Route::currentRouteName() == 'users.persional' ? 'current' : '' }}"><a href="{{ route('users.persional') }}">个人</a><div class="arrow"></div></li>
    </ul>
@endsection

@section('main')
    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" method="" action="">
            <div>
                <div class="layui-form-item">
                    <label class="layui-form-label">昵称:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="user_name" lay-verify="" value="{{ $user->user_name }}" autocomplete="off" placeholder="请输入昵称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">年龄:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="age" lay-verify="" value="{{ $user->age }}" autocomplete="off" placeholder="请输入年龄" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">QQ:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="qq" lay-verify="" value="{{ $user->qq }}" autocomplete="off" placeholder="请输入QQ" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">微信:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="wechat" lay-verify="" value="{{ $user->wechat }}" autocomplete="off" placeholder="请输入微信" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">电话:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="phone" lay-verify="" value="{{ $user->phone }}" autocomplete="off" placeholder="请输入电话" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item postion">
                    <label class="layui-form-label">店铺旺旺号:</label><i class="iconfont icon-wenhao" id="store"></i>
                    <div class="tip none">

                    </div>
                    <a href="#" class="tooltip">
                        <i class="iconfont icon-wenhao" id="recharge"></i>
                        <span>该旺旺号用于“代练-待发单”的数据获取授权，“代练-待发单”中会自动获取所填旺旺号的店铺订单数据。</span>
                    </a>


                    <div class="layui-input-inline">
                        <input type="text" name="store_wang_wang" lay-verify="" value="{{ $user->store_wang_wang }}" autocomplete="off" placeholder="请输入店铺旺旺号" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <button type="hidden" class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="update-persional" >确认修改</button>
                        <button type="hidden" class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="back" >返回</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
<!--START 底部-->
@section('js')
<script>
    layui.use(['form', 'table', 'upload'], function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;
        var upload = layui.upload;

        form.on('submit(back)', function(data) {
            window.location.href="{{ route('frontend.index') }}";
            return false;
        });

        form.on('submit(update-persional)', function(data) {
            $.post("{{ route('users.update-persional') }}", {data:data.field}, function (result) {
                if (result.code == 1) {
                    layer.msg(result.message, {
                        time:1500,
                        icon:6
                    })
                } else {
                    layer.msg(result.message, {
                        time:1500,
                        icon:5
                    })
                }
            });
            window.location.href="{{ route('frontend.index') }}";
            layer.closeAll();
            return false;
        });


        $('#recharge').mouseout(function(){
            $('.tips').addClass('none');
        });
        
        $('#recharge').mousemove(function(){
            $('.tips').removeClass('none');
        });
        $('#store').mouseout(function(){
            $('.tip').addClass('none');
        });
        
        $('#store').mousemove(function(){
            $('.tip').removeClass('none');
        });
        layer.tips('只想提示地精准些', '#hehe');
    });

</script>
@endsection