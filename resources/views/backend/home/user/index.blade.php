@extends('backend.layouts.main')

@section('title', ' | 用户列表')

@section('css')
    <style>
        .layui-table th, td{
            text-align:center;
        }
        .layui-form-item .layui-input-block{
            width:250px;
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
                            <li class="layui-this" lay-id="add">用户角色列表</li>
                        </ul>
                        <div class="layui-tab-content">
                            <header class="main-box-header clearfix">
                                <div class="filter-block">
                                    <form class="layui-form" action="">
                                        <div class="row">
                                            <div class=" col-xs-2">
                                                <input type="text" class="layui-input" name="id"  placeholder="账号ID" value="{{ $id }}">
                                            </div>
                                            <div class=" col-xs-2">
                                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </header>
                            <div class="layui-tab-item layui-show" id="user-list">
                                <form class="layui-form" action="">
                                    <table class="layui-table" lay-size="sm">
                                        <thead>
                                        <tr>
                                            <th>用户ID</th>
                                            <th>用户账号</th>
                                            <th>拥有角色</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>
                                                    @forelse($user->newRoles as $role)
                                                        【{{ $role->alias }}】
                                                    @empty
                                                        --
                                                    @endforelse
                                                </td>
                                                <td style="text-align: center">
                                                    <button lay-id="{{ $user->id }}" lay-name="{{ $user->name }}" lay-role-ids="{{ implode($user->newRoles()->pluck('new_roles.id')->toArray(), '-') ?? '' }}" lay-submit="" class="layui-btn layui-btn-normal" lay-filter="match">设置角色</button>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                        </tbody>
                                    </table>
                                </form>
                                {{ $users->links() }}
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-tab-content" style="display: none; padding:  0 20px" id="match">
        <form class="layui-form" action="" lay-filter="test1">
        {!! csrf_field() !!}
            <div class="layui-form-item">
                @forelse($roles as $role)
                <div class="layui-input-block">
                    <input type="checkbox" name="role_id" data-id="{{ $role->id }}" lay-skin="primary" title="{{ $role->alias }}" value="{{ $role->id }}">
                </div>
                @empty
                @endforelse
              </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="yes">确认</button>
                    <button  type="button" class="layui-btn layui-btn-normal cancel">取消</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use('form', function(){
            var form = layui.form,layer=layui.layer,laydate=layui.laydate,table=layui.table;
             // 取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
            // 配置
            form.on('submit(match)', function () {
                var id=this.getAttribute('lay-id');
                var name=this.getAttribute('lay-name');
                var roles=this.getAttribute('lay-role-ids');
                var arr=roles.split('-');
                var s = window.location.search; //先截取当前url中“?”及后面的字符串
                var page=s.getAddrVal('page');
                // 遍历数组
                // $('input[name=role_id]').attr('checked', false);
                if (arr) {
                    for(i=0;i<arr.length;i++) {
                       // $('input[data-id=' + arr[i] + ']').attr('checked', true);
                        $('input[name="role_id"][value="'+arr[i]+'"]').attr('checked', true);
                    }
                }
                form.render();
                // 弹框
                layer.open({
                    type: 1,
                    shade: 0.6,
                    title: '设置【'+name+'】的角色',
                    area: ['380px', '250px'],
                    content: $('#match')
                });
                
                // 发送数据
                form.on('submit(yes)', function(data){
                    // 获取选中的角色ID
                    var ids=[];
                    $("#match input:checkbox[name='role_id']:checked").each(function() { 
                        ids.push($(this).val());
                    });

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('home.user.match') }}",
                        data:{id:id, ids:ids, data:data.field},
                        success: function (data) {
                            layer.msg(data.message);
                            if (page) {
                                $.get("{{ route('home.user.index') }}?page="+page, function (result) {
                                    $('#user-list').html(result);
                                    form.render();
                                }, 'json');
                            } else {
                                $.get("{{ route('home.user.index') }}", function (result) {
                                    $('#user-list').html(result);
                                    form.render();
                                }, 'json');
                            }
                        }
                    });
                    layer.closeAll();
                    return false;
                });               
                return false;
            })
            // 获取路由后面的参数和值
            String.prototype.getAddrVal = String.prototype.getAddrVal||function(name){
                var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
                var data = this.substr(1).match(reg);
                return data!=null?decodeURIComponent(data[2]):null;
            }
        });
    </script>
@endsection