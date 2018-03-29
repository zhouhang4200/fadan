<table class="layui-table">
    <tr>
        <th>上传人</th>
        <th>截图说明</th>
        <th>上传时间</th>
        <th>操作</th>
    </tr>
    @foreach ($dataList as $data)
        <tr>
            <td>{{ $data['username'] }}</td>
            <td>{{ $data['description'] }}</td>
            <td>{{ $data['created_at'] }}</td>
            <td><button type="button" data-url="{{ $data['url'] }}" class="layui-btn layui-btn-normal layui-btn-sm show-image">查看</button></td>
        </tr>
    @endforeach
</table>
