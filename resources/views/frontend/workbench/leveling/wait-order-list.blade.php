
<table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>订单号</th>
            <th>买家旺旺</th>
            <th>购买单价</th>
            <th>购买数量</th>
            <th>实付金额</th>
            <th>下单时间</th>
            <th width="13%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $item)
            <tr data-no="{{ $item->tid }}">
                <td>{{ $item->tid }}</td>
                <td>{{ $item->buyer_nick }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->num }}</td>
                <td>{{ $item->payment }}</td>
                <td>{{ $item->created }}</td>
                <td>
                    <a href="{{ route('frontend.workbench.leveling.create', ['tid' => $item->tid]) }}" class="layui-btn layui-btn-normal">发布</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="11">没有数据</td></tr>
        @endforelse
        </tbody>
    </table>
{!! $orders->appends(['tid' => $tid, 'buyer_nick' => $buyerNick, 'start_date' => $startDate ])->render() !!}