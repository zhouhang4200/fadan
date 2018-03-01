@extends('backend.layouts.main')

@section('title', ' | 代练平台订单统计')

@section('css')
    <style>
        .layui-form-label {
            width:110px;
        }
        .layui-table thead tr th{
              height: 50px;
              text-align: center;
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
                    <form class="layui-form" action="" style="overflow: auto;">
                    <table class="layui-table" lay-size="sm" style="width: 3050px;">
                            <thead>
                            <tr>
                                <th>发布时间</th>
                                <th>发布单数</th>
                                <th>单旺旺号平均发单</th>
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
                                    <td>
                                    @if($paginatePlatformStatistic->complete_order_rate == 0)
                                    0%
                                    @elseif($paginatePlatformStatistic->complete_order_rate == 1)
                                    100%
                                    @else
                                    {{ $paginatePlatformStatistic->complete_order_rate ? round(bcmul($paginatePlatformStatistic->complete_order_rate, 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $paginatePlatformStatistic->revoke_order_count }}</td>
                                    <td>
                                    @if($paginatePlatformStatistic->revoke_order_rate == 0)
                                    0%
                                    @elseif($paginatePlatformStatistic->revoke_order_rate == 1)
                                    100%
                                    @else
                                    {{ $paginatePlatformStatistic->revoke_order_rate ? round(bcmul($paginatePlatformStatistic->revoke_order_rate, 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $paginatePlatformStatistic->arbitrate_order_count }}</td>
                                    <td>
                                    @if($paginatePlatformStatistic->arbitrate_order_rate == 0)
                                    0%
                                    @elseif($paginatePlatformStatistic->arbitrate_order_rate == 1)
                                    100%
                                    @else
                                    {{ $paginatePlatformStatistic->arbitrate_order_rate ? round(bcmul($paginatePlatformStatistic->arbitrate_order_rate, 100), 2) : 0 }}%
                                    @endif
                                    </td>
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
                                <tr style="color:red">
                                    <td>总计</td>
                                    <td>{{ $totalPlatformStatistics->order_count }}</td>
                                    <td>{{ $totalPlatformStatistics->wang_wang_order_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->receive_order_count }}</td>
                                    <td>{{ $totalPlatformStatistics->complete_order_count }}</td>
                                    <td>
                                    @if($totalPlatformStatistics->complete_order_rate == 0)
                                    0%
                                    @elseif($totalPlatformStatistics->complete_order_rate == 1)
                                    100%
                                    @else
                                    {{ $totalPlatformStatistics->complete_order_rate ? round(bcmul($totalPlatformStatistics->complete_order_rate, 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $totalPlatformStatistics->revoke_order_count }}</td>
                                    <td>
                                    @if($totalPlatformStatistics->revoke_order_rate == 0)
                                    0%
                                    @elseif($totalPlatformStatistics->revoke_order_rate == 1)
                                    100%
                                    @else
                                    {{ $totalPlatformStatistics->revoke_order_rate ? round(bcmul($totalPlatformStatistics->revoke_order_rate, 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $totalPlatformStatistics->arbitrate_order_count }}</td>
                                    <td>
                                    @if($totalPlatformStatistics->arbitrate_order_rate == 0)
                                    0%
                                    @elseif($totalPlatformStatistics->arbitrate_order_rate == 1)
                                    100%
                                    @else
                                    {{ $totalPlatformStatistics->arbitrate_order_rate ? round(bcmul($totalPlatformStatistics->arbitrate_order_rate, 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $totalPlatformStatistics->done_order_use_time_avg < 60 ? $totalPlatformStatistics->done_order_use_time_avg.'秒' : sec2Time($totalPlatformStatistics->done_order_use_time_avg) }}</td>
                                    <td>{{ $totalPlatformStatistics->done_order_security_deposit_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->done_order_efficiency_deposit_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->done_order_original_amount_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->done_order_original_amount }}</td>
                                    <td>{{ $totalPlatformStatistics->done_order_amount_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->done_order_amount }}</td>
                                    <td>{{ $totalPlatformStatistics->complete_order_amount_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->complete_order_amount }}</td>
                                    <td>{{ $totalPlatformStatistics->revoke_payment_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->revoke_payment }}</td>
                                    <td>{{ $totalPlatformStatistics->revoke_income_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->revoke_income }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrate_payment_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrate_payment }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrate_income_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrate_income }}</td>
                                    <td>{{ $totalPlatformStatistics->poundage_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->poundage }}</td>
                                    <td>{{ $totalPlatformStatistics->user_profit_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->user_profit }}</td>
                                    <td>{{ $totalPlatformStatistics->platform_profit_avg }}</td>
                                    <td>{{ $totalPlatformStatistics->platform_profit }}</td>
                                </tr>
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
        var form = layui.form, layer = layui.layer, laydate = layui.laydate, table = layui.table;

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