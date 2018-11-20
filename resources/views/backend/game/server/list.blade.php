<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm" style="text-align:center;">
        <thead>
        <tr>
            <th>ID</th>
            <th>游戏名</th>
            <th>区名</th>
            <th>服务器名</th>
            {{--<th>类型</th>--}}
            {{--<th>类别</th>--}}
            <th>创建时间</th>
            <th>更新时间</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($servers as $server)
            <tr>
                <td>{{ $server->id }}</td>
                <td>{{ $server->gameRegion->game->name }}</td>
                <td>{{ $server->gameRegion->name }}</td>
                <td>{{ $server->name }}</td>
                <td>{{ $server->created_at }}</td>
                <td>{{ $server->updated_at }}</td>
                <td>
                    <a class="layui-btn layui-btn-normal" href="{{ route('admin.server.edit', ['id' => $server->id]) }}">修改</a>
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="delete" lay-data="{{ $server->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>
{!! $servers->appends(['name' => $name])->render() !!}
