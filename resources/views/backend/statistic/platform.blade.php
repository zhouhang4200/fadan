@extends('backend.layouts.main')

@section('title', ' | 代练平台订单统计')

@section('css')
    <style>
        .layui-form-label {
            width:110px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>代练平台订单统计</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-20">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="layui-form-item">
                                <label class="layui-form-label">发单商户</label>
                                <div class="form-group col-xs-1">
                                    <select name="user_id" lay-filter="">                
                                        <option value="">请选择</option>
                                            @forelse($users as $user)
                                            <option value="{{ $user->user_id }}" {{ $user->user_id == $userId ? 'selected' : '' }} >{{ $user->username }}</option>
                                            @empty
                                            @endforelse
                                    </select>
                                </div>
                                <label class="layui-form-label">第三方平台</label>
                                <div class="form-group col-xs-1">
                                    <select name="third" lay-filter="">                
                                        <option value="1" selected>91平台</option>
                                    </select>
                                </div>
                                <label class="layui-form-label">游戏名称</label>
                                <div class="form-group col-xs-1">
                                    <select name="game_id" lay-filter="">                
                                        <option value="" >请选择</option>
                                        @forelse($games as $game)
                                        <option value="{{ $game->id }}" {{ $game->id == $gameId ? 'selected' : '' }} >{{ $game->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <label class="layui-form-label">发布时间</label>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                                </div>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                                </div>
                                <div class="form-group col-xs-2">
                                    <button type="submit" class="layui-btn layui-btn-normal ">查询</button>
                                    <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small">导出</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <form class="layui-form" action="">
                    <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>发布时间</th>
                                <th>发布单数</th>
                                <th>单旺旺号平均发送</th>
                                <th>被接单数</th>
                                <th>已结算单数</th>
                                <th>已结算占比</th>
                                <th>已撤销单数</th>
                                <th>已撤销占比</th>
                                <th>已仲裁单数</th>
                                <th>已仲裁占比</th>
                                <th>完单平均所用时间</th>
                                <th>完单平均安全保证金</th>
                                <th>完单平均效率保证金</th>
                                <th>完单平均来源价格</th>
                                <th>完单总来源价格</th>
                                <th>完单平均发单价格</th>
                                <th>完单总发单价格</th>
                                <th>结算平均支付</th>
                                <th>结算总支付</th>
                                <th>撤销平均支付</th>
                                <th>撤销总支付</th>
                                <th>撤销平均赔偿</th>
                                <th>撤销总赔偿</th>
                                <th>仲裁平均支付</th>
                                <th>仲裁总支付</th>
                                <th>仲裁平均赔偿</th>
                                <th>仲裁总赔偿</th>
                                <th>平均手续费</th>
                                <th>总手续费</th>
                                <th>商户平均利润</th>
                                <th>商户总利润</th>
                                <th>平台平均利润</th>
                                <th>平台总利润</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($paginatePlatformStatistics as $paginatePlatformStatistic)
                                <tr>
                                    <td>{{ $paginatePlatformStatistic->date }}</td>
                                    <td>{{ $paginatePlatformStatistic->order_count }}</td>
                                    <td>{{ $paginatePlatformStatistic->wang_wang_order_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->receive_order_count }}</td>
                                    <td>{{ $paginatePlatformStatistic->complete_order_count }}</td>
                                    <td>{{ $paginatePlatformStatistic->complete_order_rate ? bcmul($paginatePlatformStatistic->complete_order_rate, 100) : 0 }}%</td>
                                    <td>{{ $paginatePlatformStatistic->revoke_order_count }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoke_order_rate ? bcmul($paginatePlatformStatistic->revoke_order_rate, 100) : 0 }}%</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_order_count }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_order_rate ? bcmul($paginatePlatformStatistic->arbitrate_order_rate, 100) : 0 }}%</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_use_time_avg < 60 ? $paginatePlatformStatistic->done_order_use_time_avg.'秒' : sec2Time($paginatePlatformStatistic->done_order_use_time_avg) }}</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_security_deposit_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_efficiency_deposit_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_original_amount_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_original_amount }}</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_amount_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->done_order_amount }}</td>
                                    <td>{{ $paginatePlatformStatistic->complete_order_amount_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->complete_order_amount }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoke_payment_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoke_payment }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoke_income_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoke_income }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_payment_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_payment }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_income_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_income }}</td>
                                    <td>{{ $paginatePlatformStatistic->poundage_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->poundage }}</td>
                                    <td>{{ $paginatePlatformStatistic->user_profit_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->user_profit }}</td>
                                    <td>{{ $paginatePlatformStatistic->platform_profit_avg }}</td>
                                    <td>{{ $paginatePlatformStatistic->platform_profit }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        </form>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $paginatePlatformStatistics->total() }}　本页显示：{{ $paginatePlatformStatistics->count() }}
                        </div>
                            <div class="col-xs-9">
                                {{ $paginatePlatformStatistics->appends([
                                    'userId' => $userId,
                                    'third' => $third,
                                    'gameId' => $gameId,
                                    'startDate' => $startDate,
                                    'endDate' => $endDate,
                                ])->render() }}
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
    //Demo
    layui.use(['form', 'laytpl', 'element', 'laydate'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#startDate'
        });
        laydate.render({
            elem: '#endDate'
        });
    });
</script>
@endsection