@extends('backend.layouts.main')

@section('title', ' | 奖惩日志')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th, td{
            text-align: center;
        }
        .layui-input-inline {
            margin-top:10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">奖惩日志</li>
                        </ul>

                        <div class="layui-tab-content">                      
                        <form class="layui-form" method="" action="">
                            <div class="layui-input-inline" style="float:left;">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" >
                                        <input type="text" class="layui-input" value="{{ old('startDate') ?: $startDate }}" name="startDate" id="test1" placeholder="开始时间">
                                    </div>

                                    <div class="layui-input-inline" >
                                        <input type="text" class="layui-input" value="{{ old('endDate') ?: $endDate }}"  name="endDate" id="test2" placeholder="结束时间">
                                    </div>

                                    <div class="layui-input-inline" >
                                        <input type="text" class="layui-input" value="{{ old('orderNo') ?: $orderNo }}"  name="order_no" id="" placeholder="输入关联订单号">
                                    </div>

                                    <div class="layui-input-inline" >
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                        <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('punishes.record') }}" style="color:#fff">返回</a></button>
                                    </div>
                                </div>
                                </div>
                            </form>
                            <div class="layui-tab-item layui-show">

                                <table class="layui-table" lay-size="sm">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>奖惩单号</th>
                                            <th>关联订单号</th>
                                            <th>操作管理员</th>
                                            <th>类型</th>
                                            <th>操作描述</th>
                                            <th>操作时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($punishRecords as $punishRecord)
                                            <tr>
                                                <td>{{ $punishRecord->id }}</td>
                                                <td>{{ $punishRecord->punish_or_reward_no }}</td>
                                                <td>{{ $punishRecord->order_no }}</td>
                                                <td>{{ $punishRecord->admin_user_name ?? '系统' }}</td>
                                                <td>
                                                    @if($punishRecord->operate_style == 'created_at')
                                                        创建
                                                    @elseif($punishRecord->operate_style == 'deleted_at')
                                                        撤销
                                                    @else
                                                        更新
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($punishRecord->operate_style == 'created_at')
                                                        管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】，创建了奖惩记录,对应 奖惩列表 里面的序号 【{{ $punishRecord->punish_or_reward_id }}】
                                                    @elseif($punishRecord->operate_style == 'deleted_at')
                                                        管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，撤销了奖惩记录,对应 奖惩列表 里面的序号 【{{ $punishRecord->punish_or_reward_id }}】
                                                    @else
                                                        @if ($punishRecord->operate_style == 'confirm')
                                                            管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，修改了奖惩记录,将 【{{  $punishRecord->operate_style }}】 从原来的状态 【{{ config('punish.confirm')[$punishRecord->before_value] }}】 更新为 【{{ config('punish.confirm')[$punishRecord->after_value] }}】
                                                        @elseif($punishRecord->operate_style == 'status')
                                                            管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，修改了奖惩记录将 【{{  $punishRecord->operate_style }}】 从原来的状态 【{{ config('punish.status')[$punishRecord->before_value] }}】 更新为 【{{ config('punish.status')[$punishRecord->after_value] }}】
                                                        @else
                                                            管理员【{{ $punishRecord->admin_user_name ?? '系统' }}】 在 【{{ $punishRecord->created_at }}】 ，修改了奖惩记录,将 【{{  $punishRecord->operate_style }}】 从原来的状态 【{{ $punishRecord->before_value }}】 更新为 【{{ $punishRecord->after_value }}】
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $punishRecord->created_at }}</td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @if ($punishRecords)
                        {!! $punishRecords->appends([
                            'orderNo' => $orderNo,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var laydate = layui.laydate, form = layui.form;
        //常规用法
        laydate.render({
            elem: '#test1'
        });

        //常规用法
        laydate.render({
            elem: '#test2'
        });

    });
</script>
@endsection