@extends('backend.layouts.main')

@section('title', ' | 充值记录')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class="active"><span>充值记录</span></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <div class="filter-block">
                    <form class="layui-form">
                        <div class="row">
                            <div class=" col-xs-2">
                                <input type="text" name="order_no" placeholder="请输入订单号" autocomplete="off" class="layui-input" value="{{ $orderNo }}">
                            </div>
                            <div class=" col-xs-2">
                                <select class="layui-input" name="status">
                                    <option value="">所有状态</option>
                                    @foreach ($orderRechargeStatus as $k => $v)
                                        <option value="{{ $k }}" {{ $k == $status ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                            </div>
                        </div>

                    </form>
                </div>
            </header>
            <div class="main-box-body clearfix">
                <table class="layui-table layui-form" lay-size="sm">
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>用户ID</th>
                            <th>千手单号</th>
                            <th>应充游戏币数</th>
                            <th>已充游戏币数</th>
                            <th>游戏币单位</th>
                            <th>状态</th>
                            <th>产品ID</th>
                            <th>游戏ID</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataList as $data)
                            <tr>
                                <td>{{ $data->getAttributes()['order_no'] }}</td>
                                <td>{{ $data->user_id }}</td>
                                <td>{{ $data->qs_order_id }}</td>
                                <td>{{ $data->total_game_gold }}</td>
                                <td>{{ $data->charged_game_gold }}</td>
                                <td>{{ $data->game_gold_unit }}</td>
                                <td>{{ $orderRechargeStatus[$data->status] }}</td>
                                <td>{{ $data->product_id }}</td>
                                <td>{{ $data->bundle_id }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td>{{ $data->updated_at }}</td>
                                <td><a href="{{ route('app.order-charge.detail', ['id' => $data->getAttributes()['order_no']]) }}" class="layui-btn layui-btn-normal layui-btn-mini">详情</a></td>
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
                            {!! $dataList->appends(['order_no' => $orderNo])->render() !!}
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
layui.use(['form', 'laytpl', 'element'], function () {
});
</script>
@endsection
