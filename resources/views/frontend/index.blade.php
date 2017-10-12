@extends('frontend.layouts.app')

@section('title', '商家后台')

@section('content')
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
        layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                $.post('logout', {}, function(str){
                    window.location.href='/login';
                });  
                layer.close(index);
            });        
           
        });
    }

   
        
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;
    });


</script>
@endsection