@extends('backend.layouts.main')

@section('title', ' | 订单定向分配')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class=""><span>首页</span></li>
            <li class="active"><span>订单定向分配</span></li>
        </ol>
    </div>
</div>
<form class="layui-form" id="user-form">
    <div class="layui-form-item">
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="game_name"  placeholder="游戏名字" value="{{Request::input('game_name')}}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="creator_primary_user_id"  placeholder="主发单人iD" value="{{Request::input('creator_primary_user_id')}}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="gainer_primary_user_id"  placeholder="主接单人ID" value="{{Request::input('gainer_primary_user_id')}}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" type="submit" lay-submit="" lay-filter="user-search">查询</button>
        </div>
        <button class="layui-btn layui-btn-normal pull-right"   lay-submit lay-filter="user-add">添加指定游戏</button>
    </div>
</form>

<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th width="6%">ID</th>
        <th>游戏名</th>
        <th>主发单人iD</th>
        <th>主接单人ID</th>
        <th width="16%">添加时间</th>
        <th width="16%">更新时间</th>
        <th width="16%">操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($orienteds as $item)
        <tr data-id="{{ $item->id }}">
            <td>{{ $item->id }}</td>
            <td>{{ $item->game->name }}</td>
            <td>{{ $item->creator_primary_user_id }}</td>
            <td>{{ $item->gainer_primary_user_id }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->updated_at }}</td>
            <td>
                <button class="layui-btn layui-btn-normal layui-btn-small delete" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-game">删除</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10">没有搜索到相关数据</td>
        </tr>
    @endforelse
    </tbody>
</table>
<div id="category-add" style="display: none;padding: 20px">
    <form class="layui-form layui-form-pane" action="" id="category-add-form">
        <input type="hidden" name="type" value="">
        <div class="layui-form-item">
            <label class="layui-form-label">主发单人ID</label>
            <div class="layui-input-inline">
                <input type="text" name="creator_primary_user_id" lay-verify="required" placeholder="主发单人ID" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">主接单人ID</label>
            <div class="layui-input-inline">
                <input type="text" name="gainer_primary_user_id" lay-verify="required" placeholder="主接单人ID" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <select name="game_id" lay-verify="required" lay-search="">
                <option value="">请选择游戏</option>
                @foreach ($games as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="add-game">确定添加</button>
        </div>
    </form>
</div>
{{ $orienteds->appends(Request::all())->links() }}
@endsection
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form ,layer = layui.layer ,element = layui.element;

            // 按用户ID添加
            form.on('submit(user-add)', function (data) {

                $('#user-add-form > input[name=type]').val(data.elem.getAttribute('data-type'));
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加指定游戏',
                    area: ['600px', '630px'],
                    content: $('#category-add')
                });
                return false;
            });

            // 添加
            form.on('submit(add-game)', function (data) {
                $.post('{{ route('frontend.user.oriented.store') }}', {game_id:data.field.game_id,creator_primary_user_id:data.field.creator_primary_user_id,gainer_primary_user_id:data.field.gainer_primary_user_id}, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        window.location.reload(true)
                        return;
                    }
                }, 'json');
                return false;
            });

            // 删除
            form.on('submit(delete-game)', function (data) {
                $.post('{{ route('frontend.user.oriented.delete') }}', {id:$(this).attr('data-id')}, function (result) {
                    layer.closeAll();
                    layer.msg(result.message);
                    if (result.status == 1) {
                        window.location.reload(true)
                        return;
                    }
                }, 'json');
                return false;
            });

            // 取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
        });
    </script>
@endsection
