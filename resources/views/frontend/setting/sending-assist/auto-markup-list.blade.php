<form class="layui-form" method="" action="">
    <table class="layui-table">
        <thead>
        <tr>
            <th style="width:10%">价格区间</th>
            <th>加价开始时间</th>
            <th>加价类型</th>
            <th>增加金额</th>
            <th>加价频率</th>
            <th>加价次数限制</th>
            <th style="width:15%;">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orderAutoMarkups as $k => $orderAutoMarkup)
            <tr>
                <td>{{ $orderAutoMarkup->markup_amount == intval($orderAutoMarkup->markup_amount) ? '发单价 <= '.intval($orderAutoMarkup->markup_amount) : '发单价 <= '.$orderAutoMarkup->markup_amount }}</td>
                <td>{{ $orderAutoMarkup->markup_time > 60 ? bcdiv($orderAutoMarkup->markup_time, 60, 0).'小时'. $orderAutoMarkup->markup_time % 60 . '分钟' : $orderAutoMarkup->markup_time . '分钟' }}</td>
                <td>{{ $orderAutoMarkup->markup_type == 1 ? '百分比' : '绝对值' }}</td>
                <td>{{ $orderAutoMarkup->markup_money == intval($orderAutoMarkup->markup_money) ? intval($orderAutoMarkup->markup_money) : $orderAutoMarkup->markup_money }}</td>
                <td>{{ $orderAutoMarkup->markup_frequency }}</td>
                <td>{{ $orderAutoMarkup->markup_number }}</td>
                <td>
                    <div style="text-align: center">
                    <a href="{{ route('frontend.setting.sending-assist.auto-markup.edit', ['id' => $orderAutoMarkup->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini">编辑</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-del-id="{{ $orderAutoMarkup->id }}" lay-filter="delete">删除</button>
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
    {{ $orderAutoMarkups->links() }}
</form>