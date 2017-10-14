@extends('frontend.layouts.app')

@section('title', '账号 - 登陆记录')

@section('submenu')
@include('frontend.account.submenu')
@endsection

@section('content')
<form class="layui-form" method="" action="">
    <div class="layui-inline" style="float:left">
        <div class="layui-form-item">
            <label class="layui-form-label">账号名</label>
            <div class="layui-input-inline">
                <input type="text" name="name" value="{{ $name ?: '' }}" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
            <label class="layui-form-label">开始时间</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="年-月-日">
            </div>

            <label class="layui-form-label">结束时间</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="年-月-日">
            </div>
        </div>
    </div>
    <div style="float: left">
        <div class="layui-inline" >
            <button class="layui-btn" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
            <button  class="layui-btn"><a href="{{ route('loginrecord.index') }}" style="color:#fff">返回</a></button>
        </div>
    </div>
</form>

<div class="layui-tab-item layui-show" lay-size="sm">
    <table class="layui-table">
        <colgroup>
            <col width="150">
            <col width="200">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th>用户ID</th>
            <th>用户名</th>
            <th>登录IP</th>
            <th>登录城市</th>
            <th>登录时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($loginRecords as $loginRecord)
            <tr>
                <td>{{ $loginRecord->id }}</td>
                <td>{{ $loginRecord->user_id }}</td>
                <td>{{ $loginRecord->user->name }}</td>
                <td>{{ long2ip($loginRecord->ip) }}</td>
                <td>{{ $loginRecord->city ? $loginRecord->city->name : '' }}</td>
                <td>{{ $loginRecord->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{!! $loginRecords->appends([
'name' => $name,
'startDate' => $startDate,
'endDate' => $endDate,
])->render() !!}

@endsection

@section('js')
<script>
     layui.use('laydate', function(){
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