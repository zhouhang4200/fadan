<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th>订单号</th>
        <th>操作商户</th>
        <th>操作管理员</th>
        <th>类型</th>
        <th>操作描述</th>
        <th>操作时间</th>
    </tr>
    </thead>
    <tbody>
    @forelse($record->history as $item)
        <tr>
            <td>{{ $item->order_no }}</td>
            <td>{{ ($item->user_id == 0 || is_null($item->user_id)) ? '系统' : $item->user_id }}</td>
            <td>{{ $item->admin_user_id }}</td>
            <td>{{ config('order.operation_type')[$item->type] }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->created_at }}</td>
        </tr>
    @empty
    @endforelse
    </tbody>
</table>

