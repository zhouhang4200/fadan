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

                    @if ($accountType == 1)
                    <div class="layui-inline">
                        <label class="layui-form-mid">发单方：</label>
                        <div class="layui-input-inline">
                            <select name="creator_primary_user_id">
                                <option value="">全部</option>
                                @forelse($creatorUser as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    @else
                        <div class="layui-inline">
                            <label class="layui-form-mid">接单方：</label>
                            <div class="layui-input-inline">
                                <select name="gainer_primary_user_id">
                                    <option value="">全部</option>
                                    @forelse($gainerUser as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="layui-inline">
                        <label class="layui-form-mid">结算时间：</label>
                        <div class="layui-input-inline" style="">
                            <input type="text" class="layui-input" id="time-start" name="time_start" value="{{ Request::input('time_start') }}" placeholder="开始时间">
                        </div>
                        <div class="layui-input-inline" style="">
                            <input type="text" class="layui-input" id="time-end" name="time_end" value="{{ Request::input('time_end') }}" placeholder="结束时间">
                        </div>
                        <button class="qs-btn layui-btn-normal" type="submit">查询</button>
                        <button class="qs-btn layui-btn-normal" type="button" id="export">导出</button>
                        @if ($accountType == 2)
                            <button class="qs-btn layui-btn-normal" type="button" id="settlement">批量结算</button>
                        @endif
                    </div>
                </div>
            </form>
            <table class="layui-table layui-form" lay-size="sm">
                <thead>
                <tr>
                    <th width="14%">订单号</th>
                    <th>游戏</th>
                    <th>订单状态</th>
                    <th>结账状态</th>
                    <th>{{ $accountType == 1 ? '发单方' : '接单方' }}</th>
                    <th>最终支付金额</th>
                    <th>代练结算时间</th>
                    <th>结账时间</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $item)
                    <tr>
                        <td>天猫: {{ $item->foreign_order_no }} <br> 内部: {{ $item->order_no }}</td>
                        <td>{{ optional($item->game)->name }}</td>
                        <td>{{ config('order.status_leveling')[$item->order->status] }}</td>
                        <td>{{ $item->statusText[$item->status] }}</td>
                        <td>{{ $accountType == 1 ? $item->creator_primary_user_name : $item->gainer_primary_user_name }}
                            <br>ID: {{ $accountType == 1 ? $item->creator_primary_user_id : $item->gainer_primary_user_id  }}
                        </td>
                        <td>{{ $item->amount }}</td>
                        <td>{{ $item->finish_time }}</td>
                        <td>{{ $item->settlement_time }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="999">暂时没有数据</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $orders->appends(Request::all())->links() }}
        </div>
    </div>
@endsection

@section('pop')
    <div id="settlement-pop" style="display: none">
        <div style="padding:20px 60px;font-size: 14px">当前搜索结果中,有 <span class="count">1</span> 笔订单未结账,支付金额 <span class="total">1</span> 元,是否结账?</div>
        <div style="text-align: center;margin-bottom: 20px">
            <button class="qs-btn confirm-settlement">确定</button>&nbsp;&nbsp;&nbsp;
            <button class="qs-btn qs-btn-primary  qs-btn-table cancel">取消</button>
        </div>
    </div>
@endsection

@section('js')
    <script>
        layui.use(['laydate', 'form'], function () {
            var laydate = layui.laydate, form = layui.form, layer = layui.layer;

            laydate.render({elem: '#time-start'});
            laydate.render({elem: '#time-end'});

            $('#export').click(function () {
                var url = "{{ route('frontend.finance.month-settlement-orders.export') }}?" + $('#search').serialize();
                window.location.href = url;
            });
            $('#settlement').click(function () {
                var par = $('#search').serializeJson();
                par.type = 1;

                $.post('{{ route('frontend.finance.month-settlement-orders.settlement')  }}', par, function (result) {
                    if (result.status == 1) {
                        layer.open({
                            type:1,
                            btn:false,
                            content: $('#settlement-pop')
                        });
                        $('.count').html(result.content.count);
                        $('.total').html(result.content.total);
                    } else {
                        layer.msg('当前搜索出的订单全部为已结账');
                    }
                }, 'json');

                $('body').on('click', '.confirm-settlement', function () {
                    par.type = 2;
                    $.post('{{ route('frontend.finance.month-settlement-orders.settlement')  }}', par, function (result) {
                        if (result.status == 1) {
                            layer.closeAll();
                            layer.msg(result.message, {icon: 6},function () {
                                window.location.reload()
                            });
                        } else {
                            layer.msg(result.message, {icon: 5});
                        }
                    }, 'json');
                });
            });
        });
    </script>
@endsection