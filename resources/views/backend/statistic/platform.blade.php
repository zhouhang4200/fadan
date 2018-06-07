@extends('backend.layouts.main')

@section('title', ' | 代练平台统计')

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
                <li class="active"><span>代练平台统计</span></li>
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
                                        <option>请选择</option>            
                                        <option value="1" {{ $third == 1 ? 'selected' : ''}}>91代练</option>
                                        <option value="3"  {{ $third == 3 ? 'selected' : ''}}>蚂蚁代练</option>
                                        <option value="4"  {{ $third == 4 ? 'selected' : ''}}>DD373代练</option>
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
                                    <td>{{ $paginatePlatformStatistic->count }}</td>
                                    <td>{{ $paginatePlatformStatistic->client_wang_wang_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->count, $paginatePlatformStatistic->client_wang_wang_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->received_count }}</td>
                                    <td>{{ $paginatePlatformStatistic->completed_count }}</td>
                                    <td>
                                    @if($paginatePlatformStatistic->count == 0)
                                    0%
                                    @elseif($paginatePlatformStatistic->count != 0 && bcdiv($paginatePlatformStatistic->completed_count, $paginatePlatformStatistic->count, 2) == 1)
                                    100%
                                    @else
                                    {{ bcdiv($paginatePlatformStatistic->completed_count, $paginatePlatformStatistic->count, 2) ? round(bcmul(bcdiv($paginatePlatformStatistic->completed_count, $paginatePlatformStatistic->count, 2), 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $paginatePlatformStatistic->revoked_count }}</td>
                                    <td>
                                    @if($paginatePlatformStatistic->count == 0)
                                    0%
                                    @elseif(bcdiv($paginatePlatformStatistic->revoked_count, $paginatePlatformStatistic->count, 2) == 1)
                                    100%
                                    @else
                                    {{ bcdiv($paginatePlatformStatistic->revoked_count, $paginatePlatformStatistic->count, 2) ? round(bcmul(bcdiv($paginatePlatformStatistic->revoked_count, $paginatePlatformStatistic->count, 2), 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $paginatePlatformStatistic->arbitrationed_count }}</td>
                                    <td>
                                    @if($paginatePlatformStatistic->count == 0)
                                    0%
                                    @elseif(bcdiv($paginatePlatformStatistic->arbitrationed_count, $paginatePlatformStatistic->count, 2) == 1)
                                    100%
                                    @else
                                    {{ bcdiv($paginatePlatformStatistic->arbitrationed_count, $paginatePlatformStatistic->count, 2) ? round(bcmul(bcdiv($paginatePlatformStatistic->arbitrationed_count, $paginatePlatformStatistic->count, 2), 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : ( bcdiv($paginatePlatformStatistic->total_use_time, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count)) < 60 ? bcdiv($paginatePlatformStatistic->total_use_time, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count)).'秒' : sec2Time(bcdiv($paginatePlatformStatistic->total_use_time, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count)))) }}</td>
                                    <td>{{ $paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_security_deposit, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count), 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_efficiency_deposit, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count), 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_original_price, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count), 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_original_price }}</td>
                                    <td>{{ $paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_price, ($paginatePlatformStatistic->completed_count+$paginatePlatformStatistic->revoked_count+$paginatePlatformStatistic->arbitrationed_count), 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_price }}</td>
                                    <td>{{ $paginatePlatformStatistic->completed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_completed_price, $paginatePlatformStatistic->completed_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_completed_price }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoked_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_revoked_payment, $paginatePlatformStatistic->revoked_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_revoked_payment }}</td>
                                    <td>{{ $paginatePlatformStatistic->revoked_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_revoked_income, $paginatePlatformStatistic->revoked_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_revoked_income }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_arbitrationed_payment, $paginatePlatformStatistic->arbitrationed_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_arbitrationed_payment }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrationed_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_arbitrationed_income, $paginatePlatformStatistic->arbitrationed_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_arbitrationed_income }}</td>
                                    <td>{{ $paginatePlatformStatistic->arbitrationed_count+$paginatePlatformStatistic->revoked_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_poundage, ($paginatePlatformStatistic->arbitrationed_count+$paginatePlatformStatistic->revoked_count), 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_poundage }}</td>
                                    <td>{{ $paginatePlatformStatistic->primary_creator_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_creator_profit, $paginatePlatformStatistic->primary_creator_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_creator_profit }}</td>
                                    <td>{{ $paginatePlatformStatistic->third_count == 0 ? 0 : bcdiv($paginatePlatformStatistic->total_gainer_profit, $paginatePlatformStatistic->third_count, 2) }}</td>
                                    <td>{{ $paginatePlatformStatistic->total_gainer_profit }}</td>
                                </tr>
                            @empty
                            @endforelse
                                <tr style="color:red">
                                    <td>总计</td>
                                    <td>{{ $totalPlatformStatistics->count ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->client_wang_wang_count == 0 ? 0 : bcdiv($totalPlatformStatistics->count, $totalPlatformStatistics->client_wang_wang_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->received_count ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->completed_count ?? 0 }}</td>
                                    <td>
                                    @if($totalPlatformStatistics->count == 0)
                                    0%
                                    @elseif($totalPlatformStatistics->count != 0 && bcdiv($totalPlatformStatistics->completed_count, $totalPlatformStatistics->count, 2) == 1)
                                    100%
                                    @else
                                    {{ bcdiv($totalPlatformStatistics->completed_count, $totalPlatformStatistics->count, 2) ? round(bcmul(bcdiv($totalPlatformStatistics->completed_count, $totalPlatformStatistics->count, 2), 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $totalPlatformStatistics->revoked_count ?? 0 }}</td>
                                    <td>
                                    @if($totalPlatformStatistics->count == 0)
                                    0%
                                    @elseif(bcdiv($totalPlatformStatistics->revoked_count, $totalPlatformStatistics->count, 2) == 1)
                                    100%
                                    @else
                                    {{ bcdiv($totalPlatformStatistics->revoked_count, $totalPlatformStatistics->count, 2) ? round(bcmul(bcdiv($totalPlatformStatistics->revoked_count, $totalPlatformStatistics->count, 2), 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $totalPlatformStatistics->arbitrationed_count ?? 0 }}</td>
                                    <td>
                                    @if($totalPlatformStatistics->count == 0)
                                    0%
                                    @elseif(bcdiv($totalPlatformStatistics->arbitrationed_count, $totalPlatformStatistics->count, 2) == 1)
                                    100%
                                    @else
                                    {{ bcdiv($totalPlatformStatistics->arbitrationed_count, $totalPlatformStatistics->count, 2) ? round(bcmul(bcdiv($totalPlatformStatistics->arbitrationed_count, $totalPlatformStatistics->count, 2), 100), 2) : 0 }}%
                                    @endif
                                    </td>
                                    <td>{{ $totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count == 0 ? 0 : ( bcdiv($totalPlatformStatistics->total_use_time, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count)) < 60 ? bcdiv($totalPlatformStatistics->total_use_time, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count)).'秒' : sec2Time(bcdiv($totalPlatformStatistics->total_use_time, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count)))) }}</td>
                                    <td>{{ $totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_security_deposit, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count), 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_efficiency_deposit, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count), 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_original_price, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count), 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_original_price ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_price, ($totalPlatformStatistics->completed_count+$totalPlatformStatistics->revoked_count+$totalPlatformStatistics->arbitrationed_count), 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_price ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->completed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_completed_price, $totalPlatformStatistics->completed_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_completed_price ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->revoked_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_revoked_payment, $totalPlatformStatistics->revoked_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_revoked_payment ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->revoked_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_revoked_income, $totalPlatformStatistics->revoked_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_revoked_income ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrationed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_arbitrationed_payment, $totalPlatformStatistics->arbitrationed_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_arbitrationed_payment ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrationed_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_arbitrationed_income, $totalPlatformStatistics->arbitrationed_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_arbitrationed_income ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->arbitrationed_count+$totalPlatformStatistics->revoked_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_poundage, ($totalPlatformStatistics->arbitrationed_count+$totalPlatformStatistics->revoked_count), 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_poundage ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->primary_creator_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_creator_profit, $totalPlatformStatistics->primary_creator_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_creator_profit ?? 0 }}</td>
                                    <td>{{ $totalPlatformStatistics->third_count == 0 ? 0 : bcdiv($totalPlatformStatistics->total_gainer_profit, $totalPlatformStatistics->third_count, 2) }}</td>
                                    <td>{{ $totalPlatformStatistics->total_gainer_profit ?? 0 }}</td>
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
    layui.use(['form', 'laytpl', 'element', 'laydate'], function() {
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