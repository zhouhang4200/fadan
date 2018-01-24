@extends('backend.layouts.main')

@section('title', ' | 代练平台订单统计')

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
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="layui-form-item">
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
                                <th>完单平均代练时间</th>
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
                            @forelse($paginatePlatformOrderStatistics as $paginatePlatformOrderStatistic)
                                <tr>
                                    <td>{{ $paginatePlatformOrderStatistic->date }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->wang_wang_order_evg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->receive_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->complete_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->complete_order_rate }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_order_rate }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->arbitrate_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->arbitrate_order_rate }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->use_time_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->security_deposit_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->efficiency_deposit_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->original_amount_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_original_amount }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->amount_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_amount }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->complete_order_amount_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->complete_order_amount }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_payment_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_revoke_payment }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_income_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_revoke_income }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->complain_income_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_complain_payment }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_income_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_complain_income }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->poundage_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_poundage }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->user_profit_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->user_total_profit }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->platform_profit_avg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->platform_total_profit }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        </form>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $paginatePlatformOrderStatistics->total() }}　本页显示：{{ $paginatePlatformOrderStatistics->count() }}
                        </div>
                            <div class="col-xs-9">

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