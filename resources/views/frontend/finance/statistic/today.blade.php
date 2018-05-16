@extends('frontend.layouts.app')

@section('title', '统计 - 当日统计')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
@endsection

@section('submenu')
    @include('frontend.finance.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-input-inline">
            <div class="layui-form-item">
                <label class="layui-form-label" >发布时间</label>
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
                <th>发布日期</th>
                <th>订单号</th>
                <th>订单状态</th>
                <th>发单客服</th>
                <th>游戏名称</th>
                <th>发布价格</th>
                <th>来源价格</th>
                <th>发单支出金额</th>
                <th>撤销/仲裁退回代练费</th>
                <th>撤销/仲裁获得赔偿双金</th>
                <th>撤销/仲裁支出手续费</th>
                <th>撤销/仲裁利润</th>
                <th>完单利润</th>
                <th>该订单总利润</th>
            </tr>
            </thead>
            <tbody>
                @forelse($datas as $data)
                    <tr>
                        <td>{{ $data->date }}</td>
                        <td>{{ $data->no }}</td>
                        <td>{{ $data->status }}</td>
                        <td>{{ $data->username }}</td>
                        <td>{{ $data->game_name }}</td>
                        <td>{{ number_format($data->price, 2) }}</td>
                        <td>{{ number_format($data->original_price, 2) }}</td>
                        <td>{{ number_format($data->create_order_pay_amount, 2) }}</td>
                        <td>{{ number_format($data->revoked_and_arbitrationed_return_order_price, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->revoked_and_arbitrationed_return_deposit, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->revoked_and_arbitrationed_pay_poundage, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->revoked_and_arbitrationed_profit, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->complete_order_profit, 2) ?? '--' }}</td>
                        <td>{{ number_format($data->today_profit, 2) ?? '--' }}</td>
                    </tr>
                @empty
                @endforelse
                    
            </tbody>
        </table>
        </form>
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
            // 删除
        });
    </script>
@endsection