@extends('frontend.v1.layouts.app')

@section('title', '店铺参谋 - 宝贝运营状况')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-laypage-em {
            background-color: #ff7a00 !important;
        }
        .layui-form-label {
            width: 50px;
            padding-left: 0px;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
    <blockquote class="layui-elem-quote">
        用途：此数据以淘宝店铺宝贝为维度，统计宝贝相关淘宝订单的交易数据，供卖家参考。
    </blockquote>
    <form class="layui-form" method="" action="">
        <div class="layui-input-inline">
            <div class="layui-form-item">
                 <label class="layui-form-label">宝贝ID</label> 
                    <div class="layui-input-inline">               
                        <select name="goods_id" lay-verify="" lay-search="">
                            <option value="">请输入或选择</option>
                            @forelse($goodses as $goods)
                                <option value="{{ $goods->goods_id }}" {{ $goods->goods_id == $goodsId ? 'selected' : '' }}>{{ $goods->goods_id }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                <label class="layui-form-label" style="width: 75px;">宝贝绑定游戏</label> 
                    <div class="layui-input-inline">               
                        <select name="game_id" lay-verify="" lay-search="">
                            <option value="">请输入或选择</option>
                            @forelse($games as $game)
                                <option value="{{ $game->game_id }}" {{ $game->game_id == $gameId ? 'selected' : '' }}>{{ $game->game_name ?? '--' }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                <label class="layui-form-label" >下单时间</label>
                <div class="layui-input-inline">  
                    <input type="text" id="start_date" lay-start_date="{{ $startDate }}" class="layui-input" value="{{ $startDate ?: null }}" name="start_date" placeholder="年-月-日">
                </div>
                <div class="layui-input-inline">  
                    <input type="text" id="end_date" lay-end_date="{{ $endDate }}" class="layui-input" value="{{ $endDate ?: null }}"  name="end_date" placeholder="年-月-日">
                </div>
                <div class="layui-inline" >
                    <button class="qs-btn qs-btn-normal qs-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查询</button>
                     <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="qs-btn qs-btn-normal layui-btn-small" >导出</a>
                </div>                 
            </div>
        </div>
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
                <tr>
                    <th>宝贝名称</th>
                    <th>下单单数</th>
                    <th>下单买家数</th>
                    <th>下单金额</th>
                    <th>客单价</th>
                    <th>交易成功订单</th>
                    <th>交易成功数量</th>
                    <th>交易成功金额</th>
                    <th>交易关闭订单</th>
                    <th>交易关闭数量</th>
                    <th>交易关闭金额</th>
                </tr>
            </thead>
            <tbody>
                @if(! empty($datas) && isset($datas))
                    @forelse($datas as $data)
                        <tr>
                            <td>{{ $data->game_name ?? '' }}</td>
                            <td>{{ $data->order_count ?? 0 }}</td>
                            <td>{{ $data->buyer_count ?? 0 }}</td>
                            <td>{{ $data->order_payment+0 }}</td>
                            <td>{{ $data->success_buyer_count == 0 ? 0 : bcdiv($data->success_payment, $data->success_buyer_count, 2)+0 }}</td>
                            <td>{{ $data->success_order_count + 0 }}</td>
                            <td>{{ $data->success_goods_count + 0 }}</td>
                            <td>{{ $data->success_payment + 0 }}</td>
                            <td>{{ $data->close_order_count + 0 }}</td>
                            <td>{{ $data->close_goods_count + 0 }}</td>
                            <td>{{ $data->close_payment + 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">暂无</td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="11">暂无</td>
                    </tr>
                @endif
                @if(! empty($total))
                    <tr style="color: red;">
                        <td>总计</td>
                        <td>{{ $total->order_count ?? 0 }}</td>
                        <td>{{ $total->buyer_count ?? 0 }}</td>
                        <td>{{ $total->order_payment + 0 }}</td>
                        <td>{{ $total->success_buyer_count == 0 ? 0 : bcdiv($total->success_payment, $total->success_buyer_count, 2)+0 }}</td>
                        <td>{{ $total->success_order_count + 0 }}</td>
                        <td>{{ $total->success_goods_count + 0 }}</td>
                        <td>{{ $total->success_payment + 0 }}</td>
                        <td>{{ $total->close_order_count + 0 }}</td>
                        <td>{{ $total->close_goods_count + 0 }}</td>
                        <td>{{ $total->close_payment + 0 }}</td>
                    </tr>
                @else
                    <tr>
                        <td>总计</td>
                        <td colspan="10">暂无</td>
                    </tr>
                @endif
            </tbody>
        </table>
        </form>
        {{ $datas->appends([
            'game_id' => $gameId,
            'goods_id' => $goodsId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ])->links() }}
</div>
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laypage'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            var laypage = layui.laypage;
            //常规用法
            laydate.render({
                elem: '#start_date'
            });

            //常规用法
            laydate.render({
                elem: '#end_date'
            });
        });
    </script>
@endsection