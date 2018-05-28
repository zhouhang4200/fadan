<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>内部单号</th>
            <th>淘宝单号</th>
            <th>内部状态</th>
            <th>商户操作</th>
            <th>接单平台</th>
            <th>失败返回消息</th>
            <th>发布时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_no }}</td>
                <td>{{ $order->resource_order_no }}</td>
                <td>
                    {{ config('order.leveling_status')[$order->status] }}
                </td>
                <td>
                    {{ $order->operate }}
                </td>
                <td>{{ config('order.third')[$order->third] }}</td>
                <td>{{ $order->reason }}</td>
                <td>{{ $order->order_created_at }}</td>
                <td>
                    @if($order->status == 1)
                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="repeat" data-id="{{ $order->id }}">重发</button>
                    @endif
                    <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $order->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>

