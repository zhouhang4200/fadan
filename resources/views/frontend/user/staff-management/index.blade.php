@extends('frontend.layouts.app')

@section('title', '账号 - 员工管理')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-layer-btn{
            text-align:center !important;
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
                <label class="layui-form-label" style="width: 100px; padding-left: 0px;">员工姓名</label>
                <div class="layui-input-inline">               
                    <select name="username" lay-verify="" lay-search="">
                        <option value="">请输入员工姓名</option>
                        @forelse($children as $child)
                            <option value="{{ $child->id }}" {{ $child->id == $userName ? 'selected' : '' }}>{{ $child->username }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <label class="layui-form-label" style="width: 45px; padding-left: 0px;">账号</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" value="{{ $name ?? '' }}" name="name" placeholder="员工账号">
                </div>
                <label class="layui-form-label" style="width: 45px; padding-left: 0px;">岗位</label>
                <div class="layui-input-inline">
                    <select name="station" lay-filter="">                
                        <option value="">请输入岗位名称</option>
                        @forelse($userRoles as $userRole)
                            <option value="{{ $userRole->id }}" {{ $userRole->id == $station ? 'selected' : '' }} >{{ $userRole->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
        </div>
        <div style="float: left">
            <div class="layui-inline" >
                <button class="layui-btn layui-btn-normal layui-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查询</button>
                <a href="{{ route('staff-management.create') }}" style="color:#fff; float:right;" class="layui-btn layui-btn-normal layui-btn-small">新增</a>
            </div>
        </div>                     
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
            <tr>
                <th>编号</th>
                <th>员工姓名</th>
                <th>账号</th>
                <th>类型</th>
                <th>岗位</th>
                <th>QQ</th>
                <th>微信</th>
                <th>电话</th>
                <th>最后操作时间</th>
                <th>备注</th>
                <th>状态</th>
                <th width="15%">操作</th>
            </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ config('user.type')[$user->type] }}</td>
                        <td>{{ $user->newRoles->pluck('alias')->count() > 0 ? implode(' |
                        ', $user->newRoles->pluck('alias')->toArray()) : '--' }}</td>
                        <td>{{ $user->qq ?? '--' }}</td>
                        <td>{{ $user->wechat ?? '--' }}</td>
                        <td>{{ $user->phone ?? '--' }}</td>
                        <td>{{ $user->updated_at ?? '--' }}</td>
                        <td>{{ $user->remark ?? '--' }}</td>
                        <td><input type="checkbox" name="open" lay-data="{{ $user->id }}" {{ $user->status == 0 ? 'checked' : '' }} lay-skin="switch" lay-filter="open" lay-text="启用|禁用"></td>
                        <td>
                        @if(!$user->deleted_at)
                            <a class="layui-btn layui-btn-normal layui-btn-mini" href="{{ route('staff-management.edit', ['id' => $user->id]) }}">编辑</a>
                            <button class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-filter="delete" lay-data="{{ $user->id }}">删除</button>
                        @else
                            --
                        @endif
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
        </form>
    </div>
    {!! $users->appends([
        'name' => $name,
        'userName' => $userName,
        'station' => $station,
    ])->render() !!}

@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            // 账号启用禁用
            form.on('switch(open)', function(data){
                var id = data.elem.getAttribute('lay-data');
                $.ajax({
                    type: 'POST',
                    url: "{{ route('staff-management.forbidden') }}",
                    data:{id:id},
                    success: function (data) {
                        if (data.status) {
                            layer.msg(data.message, {icon: 6, time:1000});
                            
                        } else {
                            layer.msg('启用失败', {icon: 5, time:1500}); 
                        }
                    }
                });
            });
            //页面显示修改结果
            var succ = "{{ session('succ') }}";
            var fail = "{{ session('fail') }}";

            if (succ) {
                layer.msg(succ, {icon: 6, time:1000});
            }
            if (fail) {
                layer.msg(fail, {icon: 5, time:1000});
            }
            // 删除
            form.on('submit(delete)', function (data) {
                var id = data.elem.getAttribute('lay-data');
                console.log(id);
                layer.confirm('确认删除吗？', {
                      btn: ['确认', '取消'] 
                      ,title: '提示'
                      ,icon: 3
                    }, function(index, layero){
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('staff-management.delete') }}",
                            data:{id:id},
                            success: function (data) {
                                if (data.status) {
                                    layer.msg(data.message, {icon: 6, time:1000});
                                    
                                } else {
                                    layer.msg(data.message, {icon: 5, time:1500}); 
                                }
                            }
                        });
                        layer.closeAll();
                        window.location.href="{{ route('staff-management.index') }}";
                    }, function(index){
                        layer.closeAll();
                    });
          
                return false;
            });
        });
    </script>
@endsection