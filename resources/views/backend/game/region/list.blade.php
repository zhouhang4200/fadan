<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm" style="text-align:center;">
        <thead>
        <tr>
            <th>ID</th>
            <th>游戏名</th>
            <th>区名</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($regions as $region)
            <tr>
                <td>{{ $region->id }}</td>
                <td>{{ $region->game->name }}</td>
                <td>{{ $region->name }}</td>
                <td>{{ $region->created_at }}</td>
                <td>{{ $region->updated_at }}</td>
                <td>
                    <a class="layui-btn layui-btn-normal" href="{{ route('admin.region.edit', ['id' => $region->id]) }}">修改</a>
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="delete" lay-data="{{ $region->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>
{!! $regions->appends(['name' => $name])->render() !!}