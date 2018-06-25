@extends('frontend.v1.layouts.app')

@section('title', '财务 - 内部欠款订单')

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
                            <input type="text" class="layui-input" name="no" placeholder="相关单号" value="{{ Request::input('no') }}">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-mid">游戏：</label>
                        <div class="layui-input-inline">
                            <select name="game_id" lay-search="">
                                <option value="">请选择</option>
                                @foreach ($game as $key => $value)
                                    <option value="{{ $key }}" {{ $key == Request::input('game_id') ? 'selected' : '' }}> {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($accountType == 2)
                    <div class="layui-inline">
                        <label class="layui-form-mid">发单方：</label>
                        <div class="layui-input-inline">
                            <select name="platform">
                                <option value="">全部</option>

                            </select>
                        </div>
                    </div>
                    @else
                        <div class="layui-inline">
                            <label class="layui-form-mid">接单方：</label>
                            <div class="layui-input-inline">
                                <select name="platform">
                                    <option value="">全部</option>

                                </select>
                            </div>
                        </div>
                    @endif



                    <div class="layui-inline">
                        <label class="layui-form-mid">结算时间：</label>
                        <div class="layui-input-inline" style="">
                            <input type="text" class="layui-input" id="time-start" name="start_date" value="{{ Request::input('start_date') }}" placeholder="开始时间">
                        </div>
                        <div class="layui-input-inline" style="">
                            <input type="text" class="layui-input" id="time-end" name="end_date" value="{{ Request::input('end_date') }}" placeholder="结束时间">
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
                    <th>订单号</th>
                    <th>游戏</th>
                    <th>订单状态</th>
                    <th>结账状态</th>
                    <th>{{ $accountType == 2 ? '发单方' : '接单方' }}</th>
                    <th>支付金额</th>
                    <th>代练结算时间</th>
                    <th>结账时间</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $item)
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>{{ !empty($detail['source_order_no']) ? $detail['source_order_no'] : '' }}</td>
                        <td>{{ $item->game_name }}</td>
                        <td>{{ isset(config('order.status_leveling')[$item->status]) ? config('order.status_leveling')[$item->status] : '' }}</td>
                        <td>{{ $detail['seller_nick'] ?? '' }}</td>
                        <td>{{ $taobaoAmout + 0 }}</td>
                        <td>{{ $taobaoRefund + 0  }}</td>
                        <td>{{ $paymentAmount + 0  }}</td>
                        <td>{{ $getAmount + 0  }}</td>
                        <td>{{ $complaintAmount }}</td>
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