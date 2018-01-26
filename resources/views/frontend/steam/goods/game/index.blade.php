@extends('frontend.layouts.app')

@section('title', '商品 - 游戏列表')

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')

    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-input-inline" style="width: 200px;">

            </div>
            <div class="form-group col-xs-1">
                <button type="submit" class="layui-btn layui-btn-normal layui-btn-small pull-left">搜索</button>
            </div>
            <a class="layui-btn layui-btn-normal fr" id="add-goods-game">添加游戏</a>
        </div>
    </form>

    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>序号</th>
            <th>名称</th>
            <th>添加人</th>
            <th>修改人</th>
            <th>添加时间</th>
            <th>更新时间</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($games as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->createdAdminFrontend->name ?? '无' }}</td>
                <td>{{ $item->updatedAdminFrontend->name ?? '无' }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    {{--<button class="layui-btn layui-btn-mini layui-btn-normal" lay-submit=""
                            lay-filter="show"
                            data-route="{{ route('frontend.game.show', ['id' => $item->id]) }}">修改
                    </button>--}}
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
    {{ $games->appends(['name' => $name])->links() }}

    <div class="add-game-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">游戏名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入游戏名称" autocomplete="off"
                           class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="sortord" lay-verify="required" placeholder="请输入显示排序" autocomplete="off"
                           class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="save-game">确定添加</button>
            </div>
        </form>
    </div>

    <div class="edit-game-box" style="display: none;padding: 20px">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="id">
            <div class="layui-form-item">
                <label class="layui-form-label">游戏名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入游戏名称" autocomplete="off"
                           class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">显示排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="sortord" lay-verify="required" placeholder="请输入显示排序" autocomplete="off"
                           class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="edit">保存修改</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        layui.use('form', function () {
            var form = layui.form;
            //添加游戏
            form.on('submit(save-game)', function (data) {
                $.post('{{ route('frontend.game.store') }}', {data: data.field}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            //修改状态
            form.on('submit(change-status)', function (data) {
                $.post('{{ route('frontend.game.status') }}', {
                    id: data.elem.getAttribute('data-id'),
                    status: data.elem.getAttribute('data-status')
                }, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 查看游戏
            form.on('submit(show)', function (data) {
                $.get(data.elem.getAttribute('data-route'), {id: data.elem.getAttribute('data-id')}, function (result) {
                    $('.edit-game-box input[name="id"]').val(result.id);
                    $('.edit-game-box input[name="name"]').val(result.name);
                    $('.edit-game-box input[name="sortord"]').val(result.sortord);
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '修改游戏',
                        content: $('.edit-game-box')
                    });
                }, 'json');
                return false;
            });
            // 修改游戏
            form.on('submit(edit)', function (data) {
                $.post('{{ route('frontend.game.edit') }}', {data: data.field, id: data.field.id}, function (result) {
                    layer.msg(result.message);
                }, 'json');
                reload();
                return false;
            });
            // 添加商品弹窗
            $('#add-goods-game').on('click', function () {
                layer.open({
                    type: 1,
                    shade: 0.2,
                    title: '添加游戏',
                    content: $('.add-game-box')
                });
            });
        });
    </script>
@endsection