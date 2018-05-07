@extends('backend.layouts.main')

@section('title', ' | 用户资金流水')

@section('content')
<div class="main-box">
    <div class="main-box-body clearfix">
        <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="add">用户资金流水</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form id="search-flow" action="">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-start" name="time_start" value="{{ $timeStart }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="time-end" name="time_end" value="{{ $timeEnd }}">
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="trade_type">
                                    <option value="">所有类型</option>
                                    @foreach (config('tradetype.platform') as $key => $value)
                                        <option value="{{ $key }}" {{ $key == $tradeType ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <select class="form-control" name="trade_subtype">
                                    <option value="">所有子类型</option>
                                    @foreach (config('tradetype.platform_sub') as $key => $value)
                                        <option value="{{ $key }}" {{ $key == $tradeSubtype ? 'selected' : '' }}>{{ $key }}. {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="相关单号" name="trade_no" value="{{ $tradeNo }}">
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control" placeholder="用户ID" name="user_id" value="{{ $userId }}">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">搜索</button>
                                <a href="{{url('admin/finance/user-amount-flow/?export=1&'.http_build_query(Request::all()))}}" class="btn btn-primary" >导出</a>
                            </div>
                        </div>
                    </form>

                    <table class="layui-table" lay-size="sm">
                        <thead>
                        <tr>
                            <th>流水号</th>
                            <th>用户</th>
                            <th>管理员</th>
                            <th>类型</th>
                            <th>子类型</th>
                            <th>相关单号</th>
                            <th>金额</th>
                            <th>备注</th>
                            <th>平台资金</th>
                            <th>平台托管</th>
                            <th>用户余额</th>
                            <th>用户冻结</th>
                            <th>累计用户加款</th>
                            <th>累计用户提现</th>
                            <th>累计用户消费</th>
                            <th>累计退款给用户</th>
                            <th>累计用户支出</th>
                            <th>累计用户收入</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataList as $data)
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->user_id }}</td>
                                    <td>{{ $data->admin_user_id }}</td>
                                    <td>{{ config('tradetype.platform')[$data->trade_type] ?? $data->trade_type }}</td>
                                    <td>{{ config('tradetype.platform_sub')[$data->trade_subtype] ?? $data->trade_subtype }}</td>
                                    <td>{{ $data->trade_no }}</td>
                                    <td>{{ $data->fee + 0}}</td>
                                    <td>{{ $data->remark }}</td>
                                    <td>{{ $data->amount + 0}}</td>
                                    <td>{{ $data->managed + 0}}</td>
                                    <td>{{ $data->balance + 0}}</td>
                                    <td>{{ $data->frozen + 0}}</td>
                                    <td>{{ $data->total_recharge + 0}}</td>
                                    <td>{{ $data->total_withdraw + 0}}</td>
                                    <td>{{ $data->total_consume + 0}}</td>
                                    <td>{{ $data->total_refund + 0}}</td>
                                    <td>{{ $data->total_expend }}</td>
                                    <td>{{ $data->total_income + 0}}</td>
                                    <td>{{ $data->created_at}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $dataList->appends([
                        'user_id'       => $userId,
                        'trade_no'      => $tradeNo,
                        'trade_type'    => $tradeType,
                        'trade_subtype' => $tradeSubtype,
                        'time_start'    => $timeStart,
                        'time_end'      => $timeEnd,
                        ])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$('#time-start').datepicker();
$('#time-end').datepicker();
</script>
@endsection