@extends('frontend.layouts.app')

@section('title', '统计 - 订单统计')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>

    </style>
@endsection

@section('submenu')
    @include('frontend.statistic.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-input-inline">
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 52px;padding-left: 0px;">发布时间</label>
                <div class="layui-input-inline">  
                    <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="start_date" id="test1" placeholder="年-月-日">
                </div>
                <div class="layui-input-inline">  
                    <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="end_date" id="test2" placeholder="年-月-日">
                </div>
                <div class="layui-inline" >
                    <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查询</button>
                    <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>
                </div>                 
            </div>
        </div>
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
            <tr>
                <th>发布时间</th>
                <th>发布单数</th>
                <th>被接单数</th>
                <th>已结算单数</th>
                <th>已结算占比</th>
                <th>已撤销单数</th>
                <th>已仲裁单数</th>
                <th>已结算/撤销/仲裁来源价格</th>
                <th>已结算单发单金额</th>
                <th>撤销/仲裁支付金额</th>
                <th>撤销/仲裁获得赔偿</th>
                <th>手续费</th>
                <th>利润</th>
            </tr>
            </thead>
            <tbody>
                @forelse($datas as $data)
                    <tr>
                        <td>{{ $data->date }}</td>
                        <td>{{ $data->send_order_count }}</td>
                        <td>{{ $data->receive_order_count ?? '--' }}</td>
                        <td>{{ $data->complete_order_count ?? '--' }}</td>
                        <td>
                        @if($data->complete_order_rate == 0)
                        0%
                        @elseif($data->complete_order_rate == 1)
                        100%
                        @else
                        {{ $data->complete_order_rate ? round(bcmul($data->complete_order_rate, 100), 2).'%' : '--' }}
                        @endif
                        </td>
                        <td>{{ $data->revoke_order_count ?? '--' }}</td>
                        <td>{{ $data->arbitrate_order_count ?? '--' }}</td>
                        <td>{{ number_format($data->three_status_original_amount, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->complete_order_amount, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->two_status_payment, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->two_status_income, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->poundage, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->profit, 2) ?? '--' }}</td>
                    </tr>
                @empty
                @endforelse
                    <tr style="color:red">
                        <td>总计</td>
                        <td>{{ $totalData->total_send_order_count ?? '--' }}</td>
                        <td>{{ $totalData->total_receive_order_count ?? '--' }}</td>
                        <td>{{ $totalData->total_complete_order_count ?? '--' }}</td>
                        <td>
                        @if($totalData->total_complete_order_rate == 0)
                        0%
                        @elseif($totalData->total_complete_order_rate == 1)
                        100%
                        @else
                        {{ $totalData->total_complete_order_rate ? bcmul($totalData->total_complete_order_rate, 100).'%' : '--' }}
                        @endif
                        </td>
                        <td>{{ $totalData->total_revoke_order_count ?? '--' }}</td>
                        <td>{{ $totalData->total_arbitrate_order_count ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_three_status_original_amount, 2) ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_complete_order_amount, 2) ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_two_status_payment, 2) ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_two_status_income, 2) ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_poundage, 2) ?? '--' }}</td>
                        <td>{{ number_format($totalData->total_profit, 2) ?? '--' }}</td>
                    </tr>
            </tbody>
        </table>
        </form>
    </div>
    {!! $datas->appends([
        'start_date' => $startDate,
        'end_date' => $endDate,
    ])->render() !!}

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
            // 删除
        });
    </script>
@endsection