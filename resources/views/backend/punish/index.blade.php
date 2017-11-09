@extends('backend.layouts.main')

@section('title', ' | 违规列表')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th, td{
            text-align: center;
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
                            <li class="layui-this" lay-id="add">违规列表</li>
                        </ul>

                        <div class="layui-tab-content">                      
                        <form class="layui-form" method="" action="">

                                <div class="layui-form-item">

                                    <div class="layui-input-inline" style="width:280px">
                                    <label class="layui-form-label">账号</label>
                                        <div class="layui-input-inline">
                                            <select name="user_id" lay-verify="" lay-search="">
                                                <option value="">输入名字或直接选择</option>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $userId && $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-input-inline" style="width:280px">
                                        <label class="layui-form-label">支付状态</label>
                                            <div class="layui-input-inline">
                                                <select name="type" lay-verify="" lay-search="">
                                                    <option value="">请选择</option>
                                                    <option value="0" {{ is_numeric($type) && $type == 0 ? 'selected' : '' }}>未支付</option>
                                                    <option value="1" {{ is_numeric($type) && $type == 1 ? 'selected' : '' }}>已支付</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="layui-input-inline" style="width:280px">
                                        <label class="layui-form-label">开始时间</label>
                                        <div class="layui-input-inline">
                                        <input type="text" class="layui-input" value="{{ old('startDate') ?: $startDate }}" name="startDate" id="test1" placeholder="年-月-日">
                                        </div>
                                    </div>
                                    <div class="layui-input-inline" style="width:280px">
                                        <label class="layui-form-label">结束时间</label>
                                        <div class="layui-input-inline">
                                        <input type="text" class="layui-input" value="{{ old('endDate') ?: $endDate }}"  name="endDate" id="test2" placeholder="年-月-日">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                        <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('punishes.index') }}" style="color:#fff">返回</a></button>
                                    </div>
                                </div>
                            </form>
                            <div class="layui-tab-item layui-show">
                                <div style="padding-top:10px; padding-bottom:10px; float:right">
                                    <a href="{{ route('punishes.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal layui-btn-small">添加违规</button></a>
                                </div>
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>单号</th>
                                        <th>关联订单号</th>
                                        <th>账号id</th>
                                        <th>罚款金额</th>
                                        <th>最后期限</th>
                                        <th>状态</th>
                                        <th>时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($punishes as $punish)
                                        <tr>
                                            <td>{{ $punish->id }}</td>
                                            <td>{{ $punish->order_no }}</td>
                                            <td>{{ $punish->order_id }}</td>
                                            <td>{{ $punish->user_id }}</td>
                                            <td>{{ $punish->money }}</td>
                                            <td>{{ $punish->deadline }}</td>
                                            <td>{{ $punish->type == 1 ? '已支付' : '未支付' }}</td>
                                            <td>{{ $punish->created_at }}</td>
                                            <td>
                                            @if($punish->type == 0)
                                                <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('punishes.edit', ['punish' => $punish->id]) }}">编辑</a>
                                                <button class="layui-btn layui-btn-normal layui-btn-mini" onclick="del({{ $punish->id }})">删除</button>
                                            @endif
                                                <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('punishes.show', ['punish' => $punish->id]) }}">详情</a>
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
                            layer.msg('删除成功!', {icon: 6, time:1500},);
                            window.location.href = "{{ route('punishes.index') }}";                    
                        } else {
                            layer.msg('删除失败!', {icon: 5, time:1500},);
                        }
                    }
                });
                layer.close(index);
            });                
        });
    };
</script>
@endsection