@extends('frontend.v1.layouts.app')

@section('title', '统计 - 员工统计')

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
    <form class="layui-form" method="" action="">
        <div class="layui-input-inline">
            <div class="layui-form-item">
                <!-- <label class="layui-form-label" style="width: 50px; padding-left: 0px;">员工姓名</label>
                <div class="layui-input-inline">               
                    <select id="user_id" lay-user_id="{{ $userId }}" name="user_id" lay-verify="" lay-search="">
                        <option value="">请输入</option>
                        @if(auth()->user()->parent_id == 0)
                            <option value="{{ auth()->user()->id }}" {{ auth()->user()->id == $userId ? 'selected' : '' }}>{{ auth()->user()->username }}</option>
                        @endif
                        @forelse($children as $child)
                            <option value="{{ $child->id }}" {{ $child->id == $userId ? 'selected' : '' }}>{{ $child->username }}</option>
                        @empty
                        @endforelse
                        <option value="0" {{ 0 == $userId ? 'selected' : '' }}>所有</option>
                    </select>
                </div> -->
<!--                 <label class="layui-form-label" style="width: 50px; padding-left: 0px;">订单状态</label>
                <div class="layui-input-inline">               
                    <select id="status" lay-status="{{ $status }}" name="status" lay-verify="" lay-search="">
                        <option value="" {{ '' == $status ? 'selected' : '' }}>所有</option>
                        @forelse($statuses as $key => $value)
                            <option value="{{ $key }}" {{ $key == $status ? 'selected' : '' }}>{{ $value }}</option>
                        @empty
                        @endforelse
                    </select>
                </div> -->
                <label class="layui-form-label">员工姓名</label> 
                    <div class="layui-input-inline">               
                        <select name="user_id" lay-verify="" lay-search="">
                            <option value="">请输入员工姓名</option>
                            <option value="{{ $parent->id }}" {{ $parent->id == $userId ? 'selected' : '' }}>{{ $parent->username ?? '--' }}</option>
                            @forelse($children as $child)
                                <option value="{{ $child->id }}" {{ $child->id == $userId ? 'selected' : '' }}>{{ $child->username ?? '--' }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                <label class="layui-form-label" >发布时间</label>
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
                <!-- <th>发布日期</th>
                <th>订单号</th>
                <th>发布时间</th>
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
                <th>该订单总利润</th> -->

                <th>员工</th>
                <th>发布数量</th>
                <th>来源价格</th>
                <th>发布价格</th>
                <th>来源/发布差价</th>
                <th>已结算单数</th>
                <th>已结算发单金额</th>
                <th>已撤销单数</th>
                <th>已仲裁单数</th>
                <th>利润</th>
            </tr>
            </thead>
            <tbody>
               <!--  @forelse($datas as $data)
                    <tr>
                        <td>{{ $data->date }}</td>
                        <td>{{ $data->no }}</td>
                        <td>{{ $data->created_at }}</td>
                        <td>{{ config('order.status_leveling')[$data->status] ?? '无' }}</td>
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
                @endforelse -->
                @if(! empty($totals) && isset($totals))
                    @if (is_array($totals))
                        @forelse($totals as $total)
                            <!-- <tr style="color: red;">
                                <td>总计</td>
                                <td>{{ $total->count }} 单</td>
                                <td>--</td>
                                <td>--</td>
                                <td>--</td>
                                <td>--</td>
                                <td>{{ number_format($total->price, 2) }}</td>
                                <td>{{ number_format($total->original_price, 2) }}</td>
                                <td>{{ number_format($total->create_order_pay_amount, 2) }}</td>
                                <td>{{ number_format($total->revoked_and_arbitrationed_return_order_price, 2) ?? '--' }}</td>
                                <td>{{ number_format($total->revoked_and_arbitrationed_return_deposit, 2) ?? '--' }}</td>
                                <td>{{ number_format($total->revoked_and_arbitrationed_pay_poundage, 2) ?? '--' }}</td>
                                <td>{{ number_format($total->revoked_and_arbitrationed_profit, 2) ?? '--' }}</td>
                                <td>{{ number_format($total->complete_order_profit, 2) ?? '--' }}</td>
                                <td>{{ number_format($total->today_profit, 2) ?? '--' }}</td>
                            </tr> -->
                            <tr>
                                <td>{{ $total->username }}</td>
                                <td>{{ $total->count }}</td>
                                <td>{{ number_format($total->original_price, 2) }}</td>
                                <td>{{ number_format($total->price, 2) }}</td>
                                <td>{{ number_format($total->diff_price, 2) }}</td>
                                <td>{{ $total->complete_count }}</td>
                                <td>{{ number_format($total->complete_price, 2) ?? '--' }}</td>
                                <td>{{ $total->revoked_count }}</td>
                                <td>{{ $total->arbitrationed_count }}</td>
                                <td>{{ number_format($total->today_profit, 2) ?? '--' }}</td>
                            </tr>
                        @empty
                        @endforelse
                    @else 
                        <tr>
                            <td>{{ $totals->username }}</td>
                            <td>{{ $totals->count }}</td>
                            <td>{{ number_format($totals->original_price, 2) }}</td>
                            <td>{{ number_format($totals->price, 2) }}</td>
                            <td>{{ number_format($totals->diff_price, 2) }}</td>
                            <td>{{ $totals->complete_count }}</td>
                            <td>{{ number_format($totals->complete_price, 2) ?? '--' }}</td>
                            <td>{{ $totals->revoked_count }}</td>
                            <td>{{ $totals->arbitrationed_count }}</td>
                            <td>{{ number_format($totals->today_profit, 2) ?? '--' }}</td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="10">暂无</td>
                    </tr>
                @endif
                @if(! empty($final))
                <tr style="color: red;">
                        <td>总计:  {{ $final->creator_count }}</td>
                        <td>{{ $final->count ?? 0 }}</td>
                        <td>{{ number_format($final->original_price, 2) }}</td>
                        <td>{{ number_format($final->price, 2) }}</td>
                        <td>{{ number_format($final->diff_price, 2) }}</td>
                        <td>{{ $final->complete_count ?? 0 }}</td>
                        <td>{{ number_format($final->complete_price, 2) ?? '--' }}</td>
                        <td>{{ $final->revoked_count ?? 0 }}</td>
                        <td>{{ $final->arbitrationed_count ?? 0 }}</td>
                        <td>{{ number_format($final->today_profit, 2) ?? '--' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        </form>
    </div>
        <div id="render" lay-count="{{ $total->count ?? 0 }}" lay-page="{{ $page ?? 0 }}"></div>
    </div>
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
           
            var count = document.getElementById('render').getAttribute('lay-count');
            // var user_id= document.getElementById('user_id').getAttribute('lay-user_id');
            // var status= document.getElementById('status').getAttribute('lay-status');
            // var start_date = document.getElementById('start_date').getAttribute('lay-start_date');
            // var end_date = document.getElementById('end_date').getAttribute('lay-end_date');
            // var page = document.getElementById('render').getAttribute('lay-page');

            // laypage.render({
            //   elem: 'render'
            //   ,count: count //数据总数，从服务端得到
            //   ,curr: function(){
            //         return page ? page : 1; // 返回当前页码值
            //     }()
            //   ,jump: function(obj, first){
            //     //首次不执行
            //     if(!first){
            //         window.location.href="http://www.test.com/statistic/employee?status="+status+"&user_id="+user_id+"&start_date="+start_date+"&end_date="+end_date+"&page="+obj.curr+"&limit="+obj.limit;
            //     }
            //   }
            // });

        });
    </script>
@endsection