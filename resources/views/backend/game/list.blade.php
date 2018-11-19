<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm" style="text-align:center;">
        <thead>
        <tr>
            <th>ID</th>
            <th>游戏名</th>
            {{--<th>类型</th>--}}
            {{--<th>类别</th>--}}
            <th>创建时间</th>
            <th>更新时间</th>
            <th>是否显示</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($games as $game)
            <tr>
                <td>{{ $game->id }}</td>
                <td>{{ $game->name }}</td>
                <td>{{ $game->created_at }}</td>
                <td>{{ $game->updated_at }}</td>
                <td>
                    <div class="layui-form-item" pane="">
                        <div class="layui-input-block">
                            <input type="checkbox" {{ $game->status == 1 ? 'checked' : '' }} lay-id="{{ $game->id }}" name="status" lay-skin="switch" lay-filter="switchTest" title="">
                        </div>
                    </div>
                </td>
                <td>
                    <a class="layui-btn layui-btn-normal layui-btn-mini" href="{{ route('admin.game.edit', ['id' => $game->id]) }}">修改</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-filter="delete" lay-data="{{ $game->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>
