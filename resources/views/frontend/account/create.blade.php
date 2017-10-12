@extends('frontend.layouts.app')

@section('title', '商家后台')

@section('css')
    <style>
        .layui-form-label {
            width:65px;
        }
    </style>
@endsection

@section('content')
<!--START 主体-->
    <div class="main">
        <div class="wrapper">
            @include('frontend.layouts.account-left')
            <div class="right">
                <div class="content">

                    <div class="path"><span>添加子账号</span></div>

                    <div class="layui-tab">
                        
                        <div class="layui-tab-content">
                            <form class="layui-form" method="POST" action="{{ route('accounts.store') }}">
                            {!! csrf_field() !!}
                                <div style="width: 40%">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">账号:</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="name" lay-verify="title" value="{{ old('name') }}" autocomplete="off" placeholder="请输入标题" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">邮箱:</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="email" lay-verify="required" value="{{ old('email') }}" placeholder="请输入" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">密码:</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="password" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">确认密码:</label>
                                        <div class="layui-input-block">
                                            <input type="password" name="password_confirmation" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                                    </div>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--END 主体-->
@endsection
<!--START 底部-->
@section('js')
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
@endsection