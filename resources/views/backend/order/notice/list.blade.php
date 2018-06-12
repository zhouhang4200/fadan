<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>内部单号</th>
            <th>淘宝单号</th>
            <th>内部状态</th>
            <th>商户操作</th>
            <th>发单商户</th>
            <th>接单平台</th>
            <th>失败返回消息</th>
            <th>发布时间</th>
            <th>报错时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_no }}</td>
                <td>{{ $order->source_order_no }}</td>
                <td>
                    {{ config('order.status_leveling')[$order->status] ?? '' }}
                </td>
                <td>{{ $order->operate }} </td>
                <td>{{ isset($order->order) ? (\App\Models\User::find($order->order->creator_user_id)->username ?? '') : '' }} </td>
                <td>{{ config('order.third')[$order->third] }}</td>
                <td>{{ $order->reason }}</td>
                <td>{{ $order->order_created_at }}</td>
                <td>{{ $order->notice_created_at }}</td>
                <td>
                    @if(in_array($order->status, [1, 22, 24]) && $order->function_name != 'create')
                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="repeat" data-id="{{ $order->id }}">重发</button>
                    @endif
                    <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $order->id }}">删除</button>
                    @if ($order->status == 14)
                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="complete" data-no="{{ $order->order_no }}">完成</button>
                    @endif
                    @if ($order->status == 15)
                        <?php $consult = \App\Models\LevelingConsult::where('order_no', $order->order_no)->first() ?? ''; ?>
                        @if(! empty($consult) && $consult->complete == 0 && $consult->consult == 1) 
                            <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="cancel-revoke" data-no="{{ $order->order_no }}">取消撤销</button>
                        @elseif(! empty($consult) && $consult->complete != 1 && $consult->consult == 2)
                            <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="agree-revoke" data-no="{{ $order->order_no }}">同意撤销</button>
                            <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="refuse-revoke" data-no="{{ $order->order_no }}">不同意撤销</button>
                        @endif
                    @endif
                    @if ($order->status == 16)
                        <?php $consult = \App\Models\LevelingConsult::where('order_no', $order->order_no)->first() ?? ''; ?>
                        @if(! empty($consult) && $consult->complete == 0 && $consult->complain == 1) 
                            <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="cancel-arbitration" data-no="{{ $order->order_no }}">取消仲裁</button>
                        @elseif(! empty($consult) && $consult->complete != 1 && $consult->complain == 2)
                            @if ($consult->consult ==2)
                                <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="agree-revoke" data-no="{{ $order->order_no }}">同意撤销</button>
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>

