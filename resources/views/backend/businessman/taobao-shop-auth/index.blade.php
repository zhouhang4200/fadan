@extends('backend.layouts.main')

@section('title', ' | 店铺授权管理')

@section('css')
<style>
#shop-select dd.layui-this {background: #fff !important;}
#shop-select .multiple a {text-decoration: none; height: 25px;}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class="active"><span>店铺授权管理</span></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <div class="filter-block">
                    <form class="layui-form" action="" id="create-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">商户ID</label>
                            <div class="layui-input-block">
                                <input type="text" name="user_id" required  lay-verify="required" placeholder="整数" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">选择店铺</label>
                            <div class="layui-input-block" id="shop-select"></div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="create-form">授权</button>
                            </div>
                        </div>
                    </form>
                </div>
            </header>
            <div class="main-box-body clearfix">
                <table class="layui-table layui-form" lay-size="sm">
                    <thead>
                    <tr>
                        <th>商户ID</th>
                        <th>店铺名称</th>
                        <th>创建时间</th>
                        <th style="text-align: center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($dataList as $data)
                        <tr>
                            <td>{{ $data->user_id }}</td>
                            <td>{{ $data->wang_wang }}</td>
                            <td>{{ $data->created_at }}</td>
                            <td style="text-align: center;">
                                <button class="layui-btn layui-btn-normal layui-btn-mini destroy" data-url="{{ route('businessman.taobao-shop-auth.destroy', $data->id) }}">删除</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="99">暂无数据</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-3">
                        总数：{{ $dataList->total() }}　本页显示：{{$dataList->count()}}
                    </div>
                    <div class="col-xs-9">
                        <div class=" pull-right">
                            {!! $dataList->appends(Request::all())->render() !!}
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
layui.use(['form', 'layer'], function() {
    var form = layui.form;
    var layer = layui.layer;

    $('.destroy').click(function () {
        var url = $(this).data('url');

        layer.confirm('确定删除吗？', function (index) {
            layer.close(index);
            $.post(url, {
                _method: 'DELETE'
            }, function (data) {
                if (data.status == 1) {
                    layer.alert('操作成功', {icon: 6},function () {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.message, {icon: 5});
                }
            }, 'json');
        });
    });

    form.on('submit(create-form)', function (data) {
        $.post("{{ route('businessman.taobao-shop-auth.store') }}", $('#create-form').serialize(), function (data) {
            if (data.status == 1) {
                layer.alert('操作成功', {icon: 6}, function () {
                    window.location.reload();
                });
            } else {
                layer.alert(data.message, {icon: 5});
            }
        }, 'json');

        return false;
    });
});

var shopsJson = {!! $shopsJson !!};

layui.config({
    base : '/'
}).extend({
    selectN: './vendor/layui_extends/selectN',
    selectM: './vendor/layui_extends/selectM',
}).use(['layer','form','jquery','selectN','selectM'], function () {
    $ = layui.jquery;
    var form = layui.form
    ,selectN = layui.selectN
    ,selectM = layui.selectM;

    //多选下拉框
    var themeIns = selectM({
        elem: '#shop-select'
        ,selected: []
        ,data: shopsJson
        ,name: 'ids'
        ,max: 10
    });
});
</script>
@endsection
