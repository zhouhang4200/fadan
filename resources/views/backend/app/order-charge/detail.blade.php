@extends('backend.layouts.main')

@section('title', ' | 充值明细')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class=""><a href="{{ route('app.order-charge.index') }}">充值记录</a></li>
            <li class="active"><span>充值明细</span></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <div class="main-box-body clearfix">
                <table class="layui-table layui-form" lay-size="sm">
                    <thead>
                        <tr>
                            <th>流水号</th>
                            <th>用户ID</th>
                            <th>订单号</th>
                            <th>游戏币数</th>
                            <th>库存ID</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataList as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->user_id }}</td>
                                <td>{{ $data->order_no }}</td>
                                <td>{{ $data->game_gold }}</td>
                                <td>{{ $data->stock_id }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td>{{ $data->updated_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="99">暂无数据</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-3">
                        总数：{{ $dataList->total() }}　本页显示：{{$dataList->count()}}
                    </div>
                    <div class="col-xs-9">
                        <div class=" pull-right">
                            {!! $dataList->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
