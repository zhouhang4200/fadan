@extends('backend.layouts.main')

@section('title', ' | 奖惩列表')

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
                            <li class="layui-this" lay-id="add">奖惩列表</li>
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
                                        <input type="text" class="layui-input" value="{{ old('no') ?: $no }}"  name="order_id" id="" placeholder="输入关联订单号">
                                    </div>

                                    <div class="layui-input-inline" >
                                        <select name="user_id" lay-verify="" lay-search="">
                                            <option value="">输入商户名或直接选择</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $userId && $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="layui-input-inline" >
                                        <select name="type" lay-verify="" lay-search="">
                                            <option value="">请选择类型</option>
                                            <option value="1" {{ is_numeric($type) && $type == 1 ? 'selected' : '' }}>奖励</option>
                                            <option value="2" {{ is_numeric($type) && $type == 2 ? 'selected' : '' }}>罚款</option>
                                            <option value="3" {{ is_numeric($type) && $type == 3 ? 'selected' : '' }}>加权重</option>
                                            <option value="4" {{ is_numeric($type) && $type == 4 ? 'selected' : '' }}>减权重</option>
                                            <option value="5" {{ is_numeric($type) && $type == 5 ? 'selected' : '' }}>禁止接单</option>
                                        </select>
                                    </div>

                                    <div class="layui-input-inline" >
                                        <select name="status" lay-verify="" lay-search="">
                                            <option value="">请选择状态</option>
                                            <option value="0" {{ is_numeric($status) && $status == 0 ? 'selected' : '' }}>禁止接单一天待处罚</option>
                                            <option value="1" {{ is_numeric($status) && $status == 1 ? 'selected' : '' }}>奖励未到账</option>
                                            <option value="2" {{ is_numeric($status) && $status == 2 ? 'selected' : '' }}>奖励已到账</option>
                                            <option value="3" {{ is_numeric($status) && $status == 3 ? 'selected' : '' }}>未交罚款</option>
                                            <option value="4" {{ is_numeric($status) && $status == 4 ? 'selected' : '' }}>已交罚款</option>
                                            <option value="5" {{ is_numeric($status) && $status == 5 ? 'selected' : '' }}>未加权重</option>
                                            <option value="6" {{ is_numeric($status) && $status == 6 ? 'selected' : '' }}>已加权重</option>
                                            <option value="7" {{ is_numeric($status) && $status == 7 ? 'selected' : '' }}>未减权重</option>
                                            <option value="8" {{ is_numeric($status) && $status == 8 ? 'selected' : '' }}>已减权重</option>
                                            <option value="9" {{ is_numeric($status) && $status == 9 ? 'selected' : '' }}>申诉中</option>
                                            <option value="10" {{ is_numeric($status) && $status == 10 ? 'selected' : '' }}>申诉驳回</option>
                                            <option value="11" {{ is_numeric($status) && $status == 11 ? 'selected' : '' }}>撤销</option>
                                            <option value="12" {{ is_numeric($status) && $status == 12 ? 'selected' : '' }}>禁止接单一天已处罚</option>
                                        </select>
                                    </div>

                                    <div class="layui-input-inline" >
                                        <select name="confirm" lay-verify="" lay-search="">
                                            <option value="">请选择商户确认项</option>
                                            <option value="0" {{ is_numeric($type) && $type == 0 ? 'selected' : '' }}>待确认</option>
                                            <option value="1" {{ is_numeric($type) && $type == 1 ? 'selected' : '' }}>已确认</option>
                                        </select>
                                    </div>

                                    <div class="layui-input-inline" style="padding:10px" >
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                        <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('punishes.index') }}" style="color:#fff">返回</a></button>
                                        <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small" >导出</a>
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
                                        <th>账号id</th>
                                        <th>类型</th>
                                        <th>状态</th>
                                        <th>罚款金额</th>
                                        <th>最后期限</th>
                                        <th>初始权重值</th>
                                        <th>权重率</th>
                                        <th>变更后的权重</th>
                                        <th>权重生效时间</th>
                                        <th>权重截止时间</th>
                                        <th>奖励金额</th>
                                        <th>商家确认</th>
                                        <th>时间</th>
                                        <th style="width:10%">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($punishes as $punish)
                                        <tr>
                                            <td>{{ $punish->id }}</td>
                                            <td>{{ $punish->order_no }}</td>
                                            <td>{{ $punish->order_id }}</td>
                                            <td>{{ $punish->user_id }}</td>
                                            <td>{{ config('punish.type')[$punish->type] }}</td>
                                            <td>{{ config('punish.status')[$punish->status] }}</td>
                                            <td>{{ $punish->sub_money ? number_format($punish->sub_money, 2) : '--' }}</td>
                                            <td>{{ $punish->deadline ?? '--' }}</td>
                                            <td>{{ $punish->before_weight_value ?? '--' }}</td>
                                            <td>{{ $punish->ratio ?? '--' }}</td>
                                            <td>{{ $punish->after_weight_value ?? '--' }}</td>
                                            <td>{{ $punish->start_time ?? '--' }}</td>
                                            <td>{{ $punish->end_time ?? '--' }}</td>
                                            <td>{{ $punish->add_money ? number_format($punish->add_money, 2) : '--' }}</td>
                                            @if($punish->confirm == 1)
                                            <td>已确认</td>
                                            @else
                                            <td>待确认</td>
                                            @endif
                                            <td>{{ $punish->created_at }}</td>
                                            <td>
                                            @if($punish->status == 9)
                                                @if(in_array($punish->type, ['1', '5']))
                                                        <button class="layui-btn layui-btn-disabled layui-btn-mini" onclick="cancel({{ $punish->id }})">撤销</button>
                                                        <a type="button" class="layui-btn layui-btn-mini layui-btn-danger" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
                                                @elseif(!in_array($punish->type, ['1', '5']) && $punish->confirm == 0)
                                                    <button class="layui-btn layui-btn-disabled layui-btn-mini" onclick="del({{ $punish->id }})">撤销</button>
                                                    <a type="button" class="layui-btn layui-btn-mini layui-btn-danger" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
                                                @elseif(!in_array($punish->type, ['1', '5']) && $punish->confirm == 1)
                                                    <a type="button" class="layui-btn layui-btn-mini layui-btn-danger" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
                                                @endif
                                            @else
                                                @if(in_array($punish->type, ['1', '5']))
                                                        <button class="layui-btn layui-btn-disabled layui-btn-mini" onclick="cancel({{ $punish->id }})">撤销</button>
                                                        <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
                                                @elseif(!in_array($punish->type, ['1', '5']) && $punish->confirm == 0)
                                                    <button class="layui-btn layui-btn-disabled layui-btn-mini" onclick="del({{ $punish->id }})">撤销</button>
                                                    <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
                                                @elseif(!in_array($punish->type, ['1', '5']) && $punish->confirm == 1)
                                                    <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
                                                @endif
                                            @endif
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        @if ($punishes)
                            {!! $punishes->appends([
                                'type' => $type,
                                'status' => $status,
                                'userId' => $userId,
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
    // 删除
    function del(id)
    {
        layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'DELETE',
                    url: '/admin/punish/punishes/'+id,
                    success: function (data) {
                        if (data.code == 1) {
                            layer.msg('撤销成功!', {icon: 6, time:1500});                            window.location.href = "{{ route('punishes.index') }}";                    
                        } else {
                            layer.msg('撤销失败!', {icon: 5, time:1500});                        }
                    }
                });
                layer.close(index);
            });                
        });
    };

    // 删除
    function cancel(id)
    {
        layui.use(['form', 'layedit', 'laydate',], function(){
            var form = layui.form
            ,layer = layui.layer;
            layer.confirm('确定撤销奖励吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    type: 'POST',
                    url: '/admin/punish/punishes/cancel/'+id,
                    success: function (data) {
                        if (data.code == 1) {
                            layer.msg('撤销奖励成功!', {icon: 6, time:1500});                            window.location.href = "{{ route('punishes.index') }}";                    
                        } else {
                            layer.msg('撤销奖励失败!', {icon: 5, time:1500});                        }
                    }
                });
                layer.close(index);
            });                
        });
    };
</script>
@endsection