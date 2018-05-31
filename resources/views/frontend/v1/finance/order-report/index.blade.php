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
                    <th>游戏</th>
                    <th>订单状态</th>
                    <th>店铺名称</th>
                    <th>接单平台</th>
                    <th>淘宝金额</th>
                    <th>最终利润</th>
                    <th>淘宝下单时间</th>
                    <th>结算时间</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $item)
                    @php
                        $detail = $item->detail->pluck('field_value', 'field_name')->toArray();
                    
                        $paymentAmount = '';
                        $getAmount = '';
                        $poundage = '';
                        $profit = '';
                        $amount = '';
                        if (in_array($item->status, [19, 20, 21])){
                           // 支付金额
                            if (in_array($item->status, [21, 19])) {
                                $amount = $item->levelingConsult->api_amount;
                            } else {
                                $amount = $item->amount;
                            }
                            // 支付金额
                            $paymentAmount = $amount !=0 ?  $amount + 0:  $item->amount + 0;
        
                            $paymentAmount = (float)$paymentAmount + 0;
                            $getAmount = (float)$getAmount + 0;
                            $poundage = (float)$poundage + 0;
                            // 利润
                            $profit = ((float)$detail['source_price'] - $paymentAmount + $getAmount - $poundage) + 0;
                        }
                    @endphp
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>
                            单号1: {{ !empty($detail['source_order_no']) ? $detail['source_order_no'] : '' }}<br/>
                            单号2: {{ !empty($detail['source_order_no_1']) ? $detail['source_order_no_1'] : '' }}<br/>
                            单号3: {{ !empty($detail['source_order_no_2']) ? $detail['source_order_no_2'] : ''}}
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
                        <td>{{ $detail['source_price'] ?? '' }}</td>
                        <td>{{ $profit }}</td>
                        <td>{{ $item->created_at }}</td>
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