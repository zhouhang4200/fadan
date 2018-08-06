@extends('backend.layouts.main')

@section('title', ' | 订单操作记录')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class="active"><span>订单操作记录</span></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="main-box">
            <header class="main-box-header clearfix">
                <form class="layui-form">
                    <div class="row">
                        <div class="form-group col-xs-2">
                            <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ Request::input('start_date') ?: date('Y-m-d') }}">
                        </div>
                        <div class="form-group col-xs-2">
                            <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ Request::input('end_date') ?: date('Y-m-d') }}">
                        </div>
                        <div class="form-group col-xs-2">
                            <select  name="type"  lay-search="">
                                @foreach($operationType as $key => $value)
                                    @if ($loop->first)
                                        @continue
                                    @endif
                                    <option value="{{ $key }}" @if($key == Request::input('type')) selected  @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-2">
                            <button type="submit" class="layui-btn layui-btn-normal">查询</button>
                        </div>
                    </div>
                </form>
            </header>
            <div class="main-box-body clearfix">
                <table class="layui-table layui-form" lay-size="sm">
                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>商户ID</th>
                        <th>主账号ID</th>
                        <th>管理员ID</th>
                        <th>操作类型</th>
                        <th>操作名称</th>
                        <th>描述</th>
                        <th>时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($dataList as $data)
                        <tr>
                            <td>{{ $data->order_no }}</td>
                            <td>{{ $data->user_id }}</td>
                            <td>{{ $data->creator_primary_user_id }}</td>
                            <td>{{ $data->admin_user_id }}</td>
                            <td>{{ $data->type }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->description }}</td>
                            <td>{{ $data->created_at }}</td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-3">
                        总数：{{ $dataList->total() }}　本页显示：{{$dataList->count()}}
                    </div>
                    <div class="col-xs-9">
                        <div class=" pull-right">
                            {!! $dataList->appends(Request::all())->render() !!}
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
layui.use(['form', 'laydate', 'layer'], function(){
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
