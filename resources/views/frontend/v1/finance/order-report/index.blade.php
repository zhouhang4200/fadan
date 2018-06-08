@extends('frontend.v1.layouts.app')

@section('title', '财务 - 资金流水')

@section('css')
    <style>
        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }
        .layui-form-mid {
            margin-right: 4px;
        }
    </style>
@endsection

@section('main')
    <div class="layui-card qs-text">
        <div class="layui-card-body">
            <form class="layui-form" id="search">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-mid">订单号：</label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" name="no" placeholder="相关单号" value="{{ $no }}">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-mid">游戏：</label>
                        <div class="layui-input-inline">
                            <select name="game_id" lay-search="">
                                <option value="">请选择</option>
                                @foreach ($game as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}> {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-mid">店铺名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" name="seller_nick" placeholder="店铺名称" value="{{ $sellerNick }}">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-mid">接单平台：</label>
                        <div class="layui-input-inline">
                            <select name="platform">
                                <option value="">全部</option>
                                @foreach (config('partner.platform') as $key => $value)
                                    <option value="{{ $key }}" @if($key == @platform) selected @endif>{{ $value['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-mid">订单状态：</label>
                        <div class="layui-input-inline">
                            <select name="status" lay-search="">
                                <option value="">请选择</option>
                                @foreach (config('order.status_leveling') as $key => $value)
                                    <option value="{{ $key }}  @if($key == $status) selected  @endif">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-mid">时间：</label>
                        <div class="layui-input-inline" style="">
                            <input type="text" class="layui-input" id="time-start" name="start_date" value="{{ $startDate }}" placeholder="开始时间">
                        </div>
                        <div class="layui-input-inline" style="">
                            <input type="text" class="layui-input" id="time-end" name="end_date" value="{{ $endDate }}" placeholder="结束时间">
                        </div>
                        <button class="qs-btn layui-btn-normal" type="submit">查询</button>
                        <button class="qs-btn layui-btn-normal" type="button" id="export">导出</button>
                    </div>
                </div>
            </form>

            <table class="layui-table" lay-size="sm">
                <colgroup>
                    <col width="150">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>内部单号</th>
                    <th>淘宝单号</th>
                    <th>补款单号</th>
                    <th>游戏</th>
                    <th>订单状态</th>
                    <th>店铺名称</th>
                    <th>接单平台</th>
                    <th>淘宝金额</th>
                    <th>淘宝退款</th>
                    <th>支付代练费用</th>
                    <th>获得赔偿金额</th>
                    <th>手续费</th>
                    <th>最终支付金额</th>
                    <th>发单客服</th>
                    <th>淘宝下单时间</th>
                    <th>代练结算时间</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $item)
                    @php
                        $detail = $item->detail->pluck('field_value', 'field_name')->toArray();

                        $taobaoAmout = 0; // 淘宝金额取值:所有淘宝订单总支付金额
                        $taobaoRefund = 0; // 淘宝退款:所有淘宝订单已退款状态的支付金额
                        $paymentAmount = 0; // 支付金额: 订单总金额 或 仲裁结果需支付的金额
                        $orgPaymentAmount = 0; // 支付金额
                        $getAmount = 0; // 获得金额: 仲裁结果需支付的金额
                        $poundage = 0; // 手续费: 只有在已仲裁 已撤销 才有值
                        $profit = 0; // 利润

                        // 已仲裁 已撤销状态时 取接口的传值 否则取订单的支付金额
                        if (in_array($item->status, [21, 19])) {
                            $paymentAmount = $item->levelingConsult->api_amount;
                            $getAmount = $item->levelingConsult->api_deposit;
                            $poundage = $item->levelingConsult->api_service;
                        } else if ($item->status == 20) {
                            $paymentAmount = $item->amount;
                        } else if ($item->status == 23) {
                            $paymentAmount = 0;
                        }
                        if (!empty($detail['source_order_no'])) {
                            // 如果不是重新下的单则计算淘宝总金额与淘宝退款总金额与利润
                            if (!isset($detail['is_repeat'])  || (isset($detail['is_repeat']) && ! $detail['is_repeat'] )) {

                                $tid = [
                                    $detail['source_order_no'],
                                    isset($detail['source_order_no_1']) ?? '',
                                    isset($detail['source_order_no_2']) ?? '',
                                ];
                                //$taobaoTrade = \App\Models\TaobaoTrade::select('tid', 'payment', 'trade_status')->whereIn('tid', array_filter($tid))->get();

                                //if ($taobaoTrade) {
                                    //foreach ($taobaoTrade as $trade) {
                                        //if ($trade->trade_status == 7) {
                                         //   $taobaoRefund = $taobaoTradeData[$detail['source_order_no']];
                                        //}
                                        //$taobaoAmout = bcadd($trade->payment, $taobaoAmout, 2);
                                    //}

                                //}
                                $taobaoAmout = isset($taobaoTradeData[$item->no]['payment']) ? $taobaoTradeData[$item->no]['payment'] : 0;
                                $taobaoAmout = isset($taobaoTradeData[$item->no]['refund']) ? $taobaoTradeData[$item->no]['refund'] : 0;
                            }
                        }
                        $profit =   ($getAmount  - $paymentAmount  - $poundage) + 0;
                    @endphp
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>{{ !empty($detail['source_order_no']) ? $detail['source_order_no'] : '' }}</td>
                        <td>
                            单号1: {{ !empty($detail['source_order_no_1']) ? $detail['source_order_no_1'] : '' }}<br/>
                            单号2: {{ !empty($detail['source_order_no_2']) ? $detail['source_order_no_2'] : ''}}
                        </td>
                        <td>{{ $item->game_name }}</td>
                        <td>{{ isset(config('order.status_leveling')[$item->status]) ? config('order.status_leveling')[$item->status] : '' }}</td>
                        <td>{{ $detail['seller_nick'] ?? '' }}</td>
                        <td>
                            @if(isset($detail['third']) && $detail['third'])
                                {{ isset(config('partner.platform')[(int)$detail['third']]) ? config('partner.platform')[(int)$detail['third']]['name'] : '' }}<br/>
                                {{ $detail['third_order_no'] }}
                            @else
                            @endif
                        </td>
                        <td>{{ $taobaoAmout + 0 }}</td>
                        <td>{{ $taobaoRefund + 0  }}</td>
                        <td>{{ $orgPaymentAmount + 0  }}</td>
                        <td>{{ $getAmount + 0  }}</td>
                        <td>{{ $poundage + 0  }}</td>
                        <td>{{ $profit + 0  }}</td>
                        <td>{{ $detail['customer_service_name'] ?? '' }}</td>
                        <td>{{ $item->taobaoTrade->created ?? '' }}</td>
                        <td>{{ $item->updated_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="999">暂时没有数据</td>
                    </tr>
                @endforelse

                </tbody>

            </table>
            {{ $orders->appends(Request::all())->links() }}

            @endsection
        </div>
    </div>
@section('js')
    <script>
        layui.use(['laydate', 'form'], function () {
            var laydate = layui.laydate;

            laydate.render({elem: '#time-start'});
            laydate.render({elem: '#time-end'});
        });

        $('#export').click(function () {
            var url = "{{ route('frontend.finance.order-report.export') }}?" + $('#search').serialize();
            window.location.href = url;
        });
    </script>
@endsection