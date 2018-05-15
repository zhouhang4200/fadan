@extends('frontend.steam.steambatchcard.layouts.app')
@section('title', "赠送记录")
@section('css')

@endsection
@section('content')
<div class="out-wrap relative">

    <div class="cm-wrap" style="width:90%">
        <!-- 表单 -->
        <div class="layui-form">
            <table class="layui-table" >
                    <thead>
                    <tr>
                        <th>游戏名称</th>
                        <th>游戏url</th>
                        <th>key</th>
                        <th width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($data)
                        @foreach(collect($data) as $item)
                        <tr data-id="{{$item->TmpGuid}}">
                            <td>{{ $item->GameName }}</td>
                            <td>{{ $item->GameUrl }}</td>
                            <td>{{ $item->TmpGuid }}</td>
                            <td>
                                <button class="layui-btn btn-del">删除</button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>

                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <div class="layui-btn add" lay-submit="" lay-filter="add">添加</div>
                    </div>
                </div>
            </table>
        </div>
    </div>
</div>
<div class="add-game" style="padding: 15px;display: none">
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">游戏名称</label>
            <div class="layui-input-block">
                <input type="text" name="gameName" autocomplete="off" placeholder="请输入游戏名称" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">游戏URL</label>
            <div class="layui-input-block">
                <input type="text" name="gameUrl" autocomplete="off" placeholder="请输入游戏url" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">key</label>
            <div class="layui-input-block">
                <input type="text" name="key" autocomplete="off" placeholder="请输入key" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <input type="hidden" name="id">
            <button class="layui-btn" lay-submit="" lay-filter="save-pwd">保存</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
    layui.use(['layer', 'form','element'], function () {

        var $ = layui.jquery, layer = layui.layer, form = layui.form, laypage = layui.laypage;

        // 弹出层
        $('.add').click (function () {
            layer.open({
                type: 1,
                title: '新增',
                closeBtn: 2,
                skin: 'layui-layer-molv',
                area: ['600px', '350px'],
                shift: 4,
                moveType: 2,
                shadeClose: false,
                content: $('.add-game')
            });
        });

        // 弹出层
        $('.btn-del').click (function () {
            var id = $(this).parents('tr').attr('data-id');
            layer.confirm('是否删除', {icon: 7, title: '删除'}, function (index) {
                $.post('{{ url("card/delGameTmp") }}', {id:id}, function (result) {
                    layer.msg(result.message)
                    location.reload()
                }, 'json');
            });
        });


        form.on('submit(save-pwd)',function (data) {
            var groupInfo = JSON.stringify(data.field);
            var index = layer.load(3, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            $.post('{{ url("card/addGame") }}', {data:groupInfo}, function (result) {
                if (result.status == 1) {
                    layer.msg(result.message)
                    location.reload()
                    return false;
                }
                layer.msg(result.message)
                layer.close(index);
            }, 'json');
            return false;
        })

    });
</script>
@endsection