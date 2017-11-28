@extends('backend.layouts.main')

@section('title', ' | 奖惩类型列表')

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
                            <li class="layui-this" lay-id="add">奖惩类型列表</li>
                        </ul>
                        <div class="layui-tab-content">                      
                            <div class="layui-tab-item layui-show">
                                <div style="padding-top:10px; padding-bottom:10px; float:right">
                                    <a href="{{ route('punish-types.create') }}" style="color:#fff"><button class="layui-btn layui-btn-normal layui-btn-small">添加奖惩类型</button></a>
                                </div>
                                <table class="layui-table" lay-size="sm">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>名称</th>
                                            <th>添加时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($punishTypes as $punishType)
                                            <tr>
                                                <td>{{ $punishType->id }}</td>
                                                <td>{{ $punishType->name }}</td>
                                                <td>{{ $punishType->created_at }}</td>
                                                <td>
                                                    <a type="button" class="layui-btn layui-btn-mini layui-btn-normal" href="{{ route('punish-types.edit', ['id' => $punishType->id]) }}">编辑</a>
                                                    <button class="layui-btn layui-btn-normal layui-btn-mini" onclick="del({{ $punishType->id }})">删除</button>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('js')
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;

        var succ = "{{ session('succ') ?: '' }}";
        var updateFail = "{{ session('updateFail') ?: '' }}";

        if(succ) {
            layer.msg(succ, {icon: 6, time:1500});
        } else if (updateFail) {
            layer.msg(updateFail, {icon: 5, time:1500});
        }
      form.render();
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
                    url: '/admin/punish/punish-types/'+id,
                    success: function (data) {
                        if (data.code == 1) {
                            layer.msg('删除成功!', {icon: 6, time:1500});
                            window.location.href = "{{ route('punish-types.index') }}";                    
                        } else {
                            layer.msg('删除失败!', {icon: 5, time:1500}); 
                        }
                    }
                });
                layer.close(index);
            });                
        });
    };
</script>
@endsection