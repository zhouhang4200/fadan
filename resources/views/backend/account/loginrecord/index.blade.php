@extends('backend.layouts.main')

@section('title', '商家后台')

@section ('css')
    <style>
        .layui-table th, .layui-table td {
            position: relative;
            padding: 9px 15px;
            min-height: 20px;
            line-height: 20px;
            font-size: 12px;
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
                            <li class="layui-this" lay-id="add">我的账号</li>
                        </ul>
                        <div class="layui-tab-content">
                        <form class="layui-form" method="" action="">
                                <div class="layui-inline" style="float:left">
                                <div class="layui-form-item">
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
                                        <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查找</button>
                                        <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('login-record.index') }}" style="color:#fff">返回</a></button>
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
                                        <th>序号</th>
                                        <th>用户ID</th>
                                        <th>用户名</th>
                                        <th>登录IP</th>
                                        <th>登录城市</th>
                                        <th>登录时间</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($loginRecords as $loginRecord)
                                        <tr>
                                            <td>{{ $loginRecord->id }}</td>
                                            <td>{{ $loginRecord->admin_user_id }}</td>
                                            <td>{{ $loginRecord->adminUser->name }}</td>
                                            <td>{{ long2ip($loginRecord->ip) }}</td>
                                            <td>{{ $loginRecord->city ? $loginRecord->city->name : '' }}</td>
                                            <td>{{ $loginRecord->created_at }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                        {!! $loginRecords->appends([
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--END 主体-->
@endsection
<!--START 底部-->
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