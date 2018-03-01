<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th>短信名称
        </th>
        <th>短信内容</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($userSmsTemplate as $item)
        <tr>
            <td width="10%">{{ $item->name }}</td>
            <td>{{ $item->contents }}</td>
            <td  width="12%">
                <button class="layui-btn layui-bg-blue layui-btn-mini template-edit" data-id="{{ $item->id }}">编辑</button>
                <button class="layui-btn layui-bg-blue layui-btn-mini " data-id="{{ $item->id }}" lay-submit="" lay-filter="template-delete">删除</button>
            </td>
        </tr>
    @empty
        <tr><td colspan="20">暂时没有模板</td></tr>
    @endforelse
    </tbody>
</table>

{{ $autoSmsTemplate->links() }}