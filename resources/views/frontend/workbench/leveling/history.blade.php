<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th>操作人</th>
        <th>操作名</th>
        <th>描述</th>
        <th>时间</th>
    </tr>
    </thead>
    <tbody>
    @forelse($dataList as $key => $data)
        <tr>
            <td>{{ $data->user->username ?? '' }}</td>
            <td>{{ $data->name }}</td>
            <td>{{ $data->description }}</td>
            <td>{{ $data->created_at }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="10">空</td>
        </tr>
    @endforelse
    </tbody>
</table>
