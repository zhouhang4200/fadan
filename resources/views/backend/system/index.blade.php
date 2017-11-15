@extends('backend.layouts.main')

@section('title', ' | 系统日志')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">系统日志</li>
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
                                        <button  class="layui-btn layui-btn-normal layui-btn-small"><a href="{{ route('system-logs.index') }}" style="color:#fff">返回</a></button>
                                    </div>
                                </div>                     
                            </form>
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                   <th>ID</th>
                                        <th>创建者</th>
                                        <th>创建时间</th>
                                        <th>详情</th>
                                        <th>模型</th>
                                        <th>字段</th>
                                        <th>变更前</th>
                                        <th>变更后</th>
                                </tr>
                                </thead>
                                <tbody>
                                     @forelse($systemLogs as $systemLog)
                                        <tr>
                                            <td>{{ $systemLog->id }}</td>
                                            <td>系统</td>
                                            <td>{{ $systemLog->created_at }}</td>
                                            <td>表: {{ $systemLog->user_table }} 用户: {{ $systemLog->user_table == 'admin_users' ? \App\Models\AdminUser::find($systemLog->user_id)->name :  \App\Models\User::find($systemLog->user_id)->name }}</td>
                                            <td>{{  $systemLog->revisionable_type }}</td>
                                            <td>{{ $systemLog->key }}</td>
                                            <td>{{ $systemLog->old_value }}</td>
                                            <td>{{ $systemLog->new_value }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        {!! $systemLogs->appends([
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ])->render() !!}
                </div>
            </div>
        </div>
    </div>
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