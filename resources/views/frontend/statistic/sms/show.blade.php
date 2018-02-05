@extends('frontend.layouts.app')

@section('title', '统计 - 短信发送详情')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.statistic.submenu')
@endsection

@section('main')

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
            <tr>
                <th>发送时间</th>
                <th>关联单号</th>
                <th>接收人</th>
                <th>短信内容</th>
            </tr>
            </thead>
            <tbody>
                @forelse($recordDetail as $item)
                    <tr>
                        <td width="10%">{{ $item->date }}</td>
                        <td width="12%">{{ $item->order_no }}</td>
                        <td width="13%">{{ $item->client_phone }}</td>
                        <td>{{ $item->contents }}</td>
                    </tr>
                @empty
                    <tr style="color:red">
                        <td colspan="10">暂时没有数据</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
        </form>
    </div>
    {!! $recordDetail->render() !!}

@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            //常规用法
            laydate.render({
                elem: '#test1'
            });

            //常规用法
            laydate.render({
                elem: '#test2'
            });
           
            //页面显示修改结果
            var empty = "{{ session('empty') }}";

            if (empty) {
                layer.msg(empty, {icon: 5, time:1000});
            }
        });
    </script>
@endsection