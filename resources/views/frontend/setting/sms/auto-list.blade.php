<table class="layui-table layui-form" lay-size="sm">
    <thead>
    <tr>
        <th>短信名称</th>
        <th>短信内容</th>
        <th>发送场景</th>
        <th>启用</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($autoSmsTemplate as $item)
        <tr>
            <td width="10%">{{ $item->name }}</td>
            <td>{{ $item->contents }}</td>
            <td  width="10%">{{ $item->purpose }}</td>
            <td  width="4%">
                <input type="checkbox" name="switch" lay-skin="switch">
            </td>
            <td  width="5%">
                <button class="layui-btn layui-bg-blue layui-btn-mini template-edit" data-id="{{ $item->id }}">编辑</button>
            </td>
        </tr>
    @empty

    @endforelse
    </tbody>
</table>