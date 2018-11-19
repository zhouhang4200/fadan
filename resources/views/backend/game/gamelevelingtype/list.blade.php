<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm" style="text-align:center;">
        <thead>
        <tr>
            <th>ID</th>
            <th>游戏名</th>
            <th>类型名</th>
            <th>手续费</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($types as $type)
            <tr>
                <td>{{ $type->id }}</td>
                <td>{{ $type->game->name }}</td>
                <td>{{ $type->name }}</td>
                <td>{{ $type->poundage }}</td>
                <td>{{ $type->created_at }}</td>
                <td>{{ $type->updated_at }}</td>
                <td>
                    <a class="layui-btn layui-btn-normal layui-btn-mini" href="{{ route('admin.leveling.edit', ['id' => $type->id]) }}">修改</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-filter="delete" lay-data="{{ $type->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>
