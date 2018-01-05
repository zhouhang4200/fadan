
<table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>订单号</th>
            <th>来源</th>
            <th>来源单价</th>
            <th>来源总价</th>
            <th>下单时间</th>
            <th width="13%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $item)
            <tr data-no="{{ $item->no }}">
                <td>{{ $item->foreign_order_no }}</td>
                <td>天猫</td>
                <td>{{ $item->single_price }}</td>
                <td>{{ $item->total_price }}</td>
                <td>{{ $item->created_at }}</td>
                <td><button class="layui-btn layui-btn-normal">编辑</button></td>
            </tr>
        @empty
            <tr><td colspan="11">没有数据</td></tr>
        @endforelse
        </tbody>
    </table>
{!! $orders->appends(['no' => $no, 'wang_wang' => $wangWang, 'start_date' => $startDate ])->render() !!}