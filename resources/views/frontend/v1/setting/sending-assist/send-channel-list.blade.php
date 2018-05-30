<form class="layui-form" method="" action="">
    <table class="layui-table">
        <thead>
        <tr>
            <th style="width:10%">游戏</th>
            <th>发布渠道</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orderAutoMarkups as $k => $orderAutoMarkup)
            <tr>
                <td>{{ $orderAutoMarkup->markup_frequency }}分/次</td>
                <td>{{ $orderAutoMarkup->markup_number }}</td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
    {{ $orderAutoMarkups->links() }}
</form>