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
                                        <input type="text" class="layui-input" value="{{ old('orderId') ?: $orderId }}"  name="order_id" id="" placeholder="输入订单号">
                                    </div>

                                    <div class="layui-input-inline" style="padding:10px" >
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
                                        <th>单号</th>
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
                                            <td>{{ \App\Models\PunishOrReward::find($punishRecord->revisionable_id) ? \App\Models\PunishOrReward::find($punishRecord->revisionable_id)->order_no : '' }}</td>
                                            <td>{{ \App\Models\PunishOrReward::find($punishRecord->revisionable_id) ? \App\Models\PunishOrReward::find($punishRecord->revisionable_id)->order_id : '' }}</td>
                                            <td>{{ $punishRecord->adminUser->name ?? '' }}</td>
                                            <td>
                                                @if($punishRecord->key == 'created_at')
                                                    {{ $punishRecord->adminUser->name ?? '' }} 创建了奖惩记录,记录序号为 {{ $punishRecord->revisionable_id }}
                                                @elseif($punishRecord->key == 'deleted_at')
                                                    {{ $punishRecord->adminUser->name ?? '' }} 撤销了奖惩记录,记录序号为 {{ $punishRecord->revisionable_id }}
                                                @else
                                                    {{ $punishRecord->adminUser->name ?? '' }} 更新了奖惩记录,将 {{ $punishRecord->key }} 从原来的值 {{ $punishRecord->old_value }} 更新为 {{ $punishRecord->new_value }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($punishRecord->key == 'created_at')
                                                    创建
                                                @elseif($punishRecord->key == 'deleted_at')
                                                    撤销
                                                @else
                                                    更新
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        @if($punishRecords)
                            {!! $punishRecords->appends([
                                'order_id' => $orderId,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
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
        var laydate = layui.laydate;
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