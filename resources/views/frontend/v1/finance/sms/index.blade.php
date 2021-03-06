@extends('frontend.v1.layouts.app')

@section('title', '统计 - 短信发送统计')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-form-label {
            float: left;
            display: block;
            padding: 9px 0;
            width: 80px;
            font-weight: 400;
             text-align: left;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
        <form class="layui-form"  action="">
            <div class="layui-input-inline">
                <div class="layui-form-item">
                    <label class="layui-form-label">发布时间</label>
                        <div class="layui-input-inline">  
                            <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="start_date" id="test1" placeholder="年-月-日">
                        </div>
                        <div class="layui-input-inline">  
                            <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="end_date" id="test2" placeholder="年-月-日">
                        </div>
                        <div class="layui-inline" >
                            <button class="qs-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px"><i class="iconfont icon-search"></i><span style="padding-left: 3px">查询</span></button>
                            <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="qs-btn layui-btn-normal layui-btn-small" ><i class="iconfont icon-logout"></i><span style="padding-left: 3px">导出</span></a>
                        </div>                 
                </div>
            </div>
        </form>

        <div class="layui-tab-item layui-show" lay-size="sm">
            <form class="layui-form" action="">
            <table class="layui-table" lay-size="sm" style="text-align:center;">
                <thead>
                <tr>
                    <th>发送时间</th>
                    <th>发送条数</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    <?php $count = 0; ?>
                    @forelse($record as $item)
                        <?php $count +=$item->count ?>
                        <tr>
                            <td width="10%">{{ $item->date }}</td>
                            <td>{{ $item->count }}</td>
                            <td width="10%">
                                <a href="{{ route('frontend.statistic.show', ['date' => $item->date]) }}" class="qs-btn layui-btn-normal layui-btn-small">详情</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">暂时没有数据</td>
                        </tr>
                    @endforelse
                    <tr  style="color:red">
                        <td  width="10%">总计</td>
                        <td>{{ $count  }}</td>
                        <td  width="10%"></td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        {!! $record->appends([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ])->render() !!}
    </div>
</div>
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