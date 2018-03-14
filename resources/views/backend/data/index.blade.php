@extends('backend.layouts.main')

@section('title', ' | 昨日数据')

@section('css')
    <style>
        .layui-table tr th, td{
            text-align: center;
        }
        .layui-form-item .layui-input-inline {
            margin-right: 10px;
            width:350px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">代充平台数据</li>
                        </ul>
                        <div class="layui-tab-content">                      
                            <form class="layui-form" method="" action="">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input" value="{{ old('date') ?: $date }}" name="date" id="test1" placeholder="日期">
                                    </div>
                            
                                    <div class="layui-input-inline">
                                        <a class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" >查找</a>
                                        <a href="{{ route('datas.index') }}" class="layui-btn layui-btn-normal layui-btn-small" style="color:#fff">返回</a>
                                        <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>
                                    </div>
                                </div>
                            </form>
                            <div class="layui-tab-item layui-show">

                                <table class="layui-table" lay-size="sm">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>数据日期</th>
                                        <th>库存托管</th>
                                        <th>库存交易</th>
                                        <th>转单市场</th>
                                        <th>慢充</th>
                                        <th>订单集市</th>
                                        <th>创建时间</th>
                                        <th>更新时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dayDatas as $dayData)
                                        <tr>
                                            <td>{{ $dayData->id }}</td>
                                            <td>{{ $dayData->date }}</td>
                                            <td>{{ $dayData->stock_trusteeship }}</td>
                                            <td>{{ $dayData->stock_transaction }}</td>
                                            <td>{{ $dayData->transfer_transaction }}</td>
                                            <td>{{ $dayData->slow_recharge }}</td>
                                            <td>{{ $dayData->order_market }}</td>
                                            <td>{{ $dayData->created_at }}</td>
                                            <td>{{ $dayData->updated_at }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        @if ($dayDatas)
                            {!! $dayDatas->appends([
                                'date' => $date,
                            ])->render() !!}
                        @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var laydate = layui.laydate, form = layui.form, layer=layui.layer;
        var miss = "{{ session('miss') ?? '' }}";
        if (miss) {
            layer.msg(miss, {icon: 5, time:1500});
        }
        //常规用法
        laydate.render({
        elem: '#test1'
        });
    });

</script>
@endsection