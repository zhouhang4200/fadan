@extends('backend.layouts.main')

@section('title', ' | 商户投诉')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商户投诉</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-left">
                        <form class="layui-form" id="user-form">
                            <div class="layui-form-item">
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="creator_primary_user_id"  placeholder="订单号" value="{{ Request::input('creator_primary_user_id') }}">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ Request::input('creator_primary_user_id') }}">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ Request::input('creator_primary_user_id') }}">
                                </div>
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-normal" type="submit" lay-submit="" lay-filter="user-search">查询</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <a href="{{ route('frontend.user.complaint.create') }}" class="layui-btn layui-btn-samll layui-btn-normal">添加</a>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th width="6%">投诉方ID</th>
                                <th width="6%">投诉方呢称</th>
                                <th>被投诉方ID</th>
                                <th>被投诉方呢称</th>
                                <th>投诉订单号</th>
                                <th>被投诉方赔偿金额</th>
                                <th>备注</th>
                                <th width="16%">处理时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($complaint as $item)
                                <tr>
                                    <td>{{ $item->complaint_primary_user_id }}</td>
                                    <td>{{ $item->complaint_primary_user_id }}</td>
                                    <td>{{ $item->be_complaint_primary_user_id  }}</td>
                                    <td>{{ $item->be_complaint_primary_user_id  }}</td>
                                    <td>{{ $item->order_no }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->remark }}</td>
                                    <td>{{ $item->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">没有搜索到相关数据</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $complaint->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
