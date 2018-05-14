@extends('frontend.layouts.app')

@section('title', '账号 - 登录记录')

@section('css')
    <style>
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
            <div class="layui-inline" style="float:left">
            <div class="layui-form-item">
            @if($user->parent_id == 0)
            <div class="layui-inline">
                <div class="layui-input-inline">
                <select name="name" lay-verify="" lay-search="">
                    <option value="">输入子账号名字或直接选择</option>
                    @forelse($users as $user)
                    <option value="{{ $user->id }}" {{ $name && $name == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @empty
                    @endforelse
                </select>
                </div>
            </div>
            @endif
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $startDate ?: null }}" name="startDate" id="test1" placeholder="开始时间">
                </div>

                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $endDate ?: null }}"  name="endDate" id="test2" placeholder="结束时间">
                </div>
            </div>
        </div>
        <div class="layui-inline" >
            <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1">查找</button>
            <a href="{{ route('login.history') }}" class="layui-btn layui-btn-normal layui-btn-small">返回</a>
        </div>
    </form>

    <div class="layui-tab-item layui-show">
        <table class="layui-table"lay-size="sm">
            <thead>
            <tr style="text-aliag:center">
                <th style="width:7%">序号</th>
                <th>账号</th>
                <th>登录IP</th>
                <th>登录城市</th>
                <th>登录时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($histories as $history)
                <tr>
                    <td>{{ $history->id }}</td>
                    <td>{{ $history->user->name }}</td>
                    <td>{{ long2ip($history->ip) }}</td>
                    <td>{{ $history->city ? $history->city->name : '' }}</td>
                    <td>{{ $history->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    
    @if (isset($name)) 
        {!! $histories->appends([
        'name' => $name,
        'startDate' => $startDate,
        'endDate' => $endDate,
        ])->render() !!}
    @else 
        {!! $histories->appends([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->render() !!}
    @endif

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