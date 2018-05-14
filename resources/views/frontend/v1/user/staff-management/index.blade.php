@extends('frontend.v1.layouts.app')

@section('title', '账号 - 员工管理')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-layer-btn{
            text-align:center !important;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
<div class="layui-card-body">
    <form class="layui-form" method="" action="" >
            <div class="layui-inline" style="float:left">
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 50px; padding-left: 0px;">员工姓名</label>
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

    <div class="layui-tab-item layui-show" lay-size="sm" id="staff">
    @include('frontend.user.staff-management.list')
        {!! $users->appends([
            'name' => $name,
            'userName' => $userName,
            'station' => $station,
        ])->render() !!}
    </div>
</div>
</div>
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            // 获取路由后面的参数
            String.prototype.getAddrVal = String.prototype.getAddrVal||function(name){
                var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
                var data = this.substr(1).match(reg);
                return data!=null?decodeURIComponent(data[2]):null;
            }
            // 账号启用禁用
            form.on('switch(open)', function(data){
                var id = data.elem.getAttribute('lay-data');
                $.ajax({
                    type: 'POST',
                    url: "{{ route('staff-management.forbidden') }}",
                    data:{id:id},
                    success: function (data) {
                        layer.msg(data.message);
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
                var s=window.location.search; //先截取当前url中“?”及后面的字符串
                var page=s.getAddrVal('page');

                layer.confirm('确认删除吗？', {
                      btn: ['确认', '取消'] 
                      ,title: '提示'
                      ,icon: 3
                    }, function(index, layers){
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('staff-management.delete') }}",
                            data:{id:id},
                            success: function (data) {
                                layer.msg(data.message);
                                if (page) {
                                    $.get("{{ route('staff-management.index') }}?page="+page, function (result) {
                                        $('#staff').html(result);
                                        form.render();
                                    }, 'json');
                                } else {
                                    $.get("{{ route('staff-management.index') }}", function (result) {
                                        $('#staff').html(result);
                                        form.render();
                                    }, 'json');
                                }
                            }
                        });
                        layer.closeAll();
                    }, function(index){
                        layer.closeAll();
                    });
                return false;
            });
        });
    </script>
@endsection