@extends('frontend.v1.layouts.app')

@section('title', '宝贝运营状况 - 宝贝外包订单')

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
                <label class="layui-form-label">宝贝名称</label> 
                    <div class="layui-input-inline">               
                        <select name="game_id" lay-verify="" lay-search="">
                            <option value="">请输入宝贝名称</option>
                            @forelse($games as $game)
                                <option value="{{ $game->id }}" {{ $game->id == $gameId ? 'selected' : '' }}>{{ $game->name ?? '--' }}</option>
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
                <th>发布时间</th>
                <th>发布单数</th>
                <th>被接单数</th>
                <th>已结算单数</th>
                <th>已结算占比</th>
                <th>已撤销单数</th>
                <th>已仲裁单数</th>
                <th>已结算/撤销/仲裁来源价格</th>
                <th>已结算单发单金额</th>
                <th>撤销/仲裁支付金额</th>
                <th>撤销/仲裁获得赔偿</th>
                <th>手续费</th>
                <th>利润</th>
            </tr>
            </thead>
            <tbody>
                @if(! empty($datas) && isset($datas))
                    @forelse($datas as $data)
                        <tr>
                            <td>{{ $data->date ?? '' }}</td>
                            <td>{{ $data->count ?? 0 }}</td>
                            <td>{{ $data->received_count ?? 0 }}</td>
                            <td>{{ $data->completed_count ?? 0 }}</td>
                            <td>{{ $data->count == 0 ? 0 : bcmul(bcdiv($data->completed_count, $data->count), 100, 2)+0 }}%</td>
                            <td>{{ $data->revoked_count + 0 }}</td>
                            <td>{{ $data->arbitrationed_count + 0 }}</td>
                            <td>{{ $data->three_original_price + 0 }}</td>
                            <td>{{ $data->completed_price + 0 }}</td>
                            <td>{{ $data->consult_amount + 0 }}</td>
                            <td>{{ $data->consult_deposit + 0 }}</td>
                            <td>{{ $data->consult_poundage + 0 }}</td>
                            <td>{{ $data->profit + 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13">暂无</td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="13">暂无</td>
                    </tr>
                @endif
                @if(! empty($total))
                    <tr style="color: red;">
                        <td>总计</td>
                        <td>{{ $total->count ?? 0 }}</td>
                        <td>{{ $total->received_count ?? 0 }}</td>
                        <td>{{ $total->completed_count ?? 0 }}</td>
                        <td>{{ $total->count == 0 ? 0 : bcmul(bcdiv($total->completed_count, $total->count), 100, 2)+0 }}%</td>
                        <td>{{ $total->revoked_count + 0 }}</td>
                        <td>{{ $total->arbitrationed_count + 0 }}</td>
                        <td>{{ $total->three_original_price + 0 }}</td>
                        <td>{{ $total->completed_price + 0 }}</td>
                        <td>{{ $total->consult_amount + 0 }}</td>
                        <td>{{ $total->consult_deposit + 0 }}</td>
                        <td>{{ $total->consult_poundage + 0 }}</td>
                        <td>{{ $total->profit + 0 }}</td>
                    </tr>
                @else
                    <tr>
                        <td>总计</td>
                        <td colspan="12">暂无</td>
                    </tr>
                @endif
            </tbody>
        </table>
        </form>
        {{ $datas->appends([
            'game_id' => $gameId,
            'start_date' =>$startDate,
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