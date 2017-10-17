@extends('frontend.layouts.app')

@section('title', '账号 - 历史记录')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-form-item .layui-input-inline {
            float: left;
            width: 120px;
            margin-right: 10px;
        }
        .layui-form-label {
            width:60px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.user.submenu')
@endsection

@section('main')
    <form class="layui-form" method="" action="">
        <div class="layui-inline" style="float:left">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">搜索选择框</label>
                    <div class="layui-input-inline">
                    <select name="name" lay-verify="required" lay-search="">
                        <option value="">输入名字或直接选择</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $name ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    </div>
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
                <button  class="layui-btn"><a href="{{ route('login.child') }}" style="color:#fff">返回</a></button>
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
            @foreach($histories as $history)
                <tr>
                    <td>{{ $history->id }}</td>
                    <td>{{ $history->user_id }}</td>
                    <td>{{ $history->user->name }}</td>
                    <td>{{ long2ip($history->ip) }}</td>
                    <td>{{ $history->city ? $history->city->name : '' }}</td>
                    <td>{{ $history->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {!! $histories->appends([
        'name' => $name,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ])->render() !!}

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