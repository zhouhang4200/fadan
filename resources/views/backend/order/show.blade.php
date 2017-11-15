@extends('backend.layouts.main')

@section('title', ' | 订单详情')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class=""><a href="{{ route('orders.index') }}"><span>订单列表</span></a></li>
                <li class="active"><span>订单详情</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <div class="layui-tab layui-tab-brief" lay-filter="detail">
                            <ul class="layui-tab-title">
                                <li class="layui-this" lay-id="content">订单内容</li>
                                <li lay-id="record">订单日志</li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show content"></div>
                                <div class="layui-tab-item record"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
<script>
    layui.use(['layer', 'element'], function () {
        var element = layui.element;

        getContent();

        element.on('tab(detail)', function(elem){
            var action = $(this).attr('lay-id');
            if (action == 'content') {
                getContent();
            } else {
                $.post('{{ route('orders.record') }}', {id:'{{ Route::input('id') }}'},function(result){
                    $('.record').html(result);
                }, 'json');
            }
        });
        // 获取订单内容
        function getContent() {
            $.post('{{ route('orders.content') }}', {id:'{{ Route::input('id') }}'},function(result){
                $('.content').html(result);
            }, 'json')
        }
    });
</script>
@endsection