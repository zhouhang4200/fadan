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
            <td  width="8%">{{ config('sms.purpose')[$item->purpose] }}</td>
            <td  width="5%">
                <input type="checkbox" name="status" lay-skin="switch" data-id="{{ $item->id }}" @if($item->status == 1) checked @endif lay-filter="test">
            </td>

            <td  width="5%">
                <button class="qs-btn qs-btn-normal qs-btn-mini template-edit" data-id="{{ $item->id }}"><i class="iconfont icon-edit"></i></button>
            </td>
        </tr>
    @empty

    @endforelse
    </tbody>
</table>