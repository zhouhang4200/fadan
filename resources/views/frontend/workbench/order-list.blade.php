<?php
// 获取当前用户ID
$currentUserId = Auth::user()->id;
// 获取主账号
$primaryUserId = Auth::user()->getPrimaryUserId();
?>

<table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>订单号</th>
            <th>类型</th>
            <th>游戏</th>
            <th>商品</th>
            <th>数量</th>
            <th>单价</th>
            <th>总价</th>
            <th>状态</th>
            <th>下单时间</th>
            <th width="13%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $item)
            <tr data-no="{{ $item->no }}">
                <td>{{ $item->no }}</td>
                <td>{{ $item->service_name }}</td>
                <td>{{ $item->game_name }}</td>
                <td>{{ $item->goods_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->amount }}</td>
                <?php $status = receivingRecordExist( $primaryUserId, $item->no) && $item->status == 1 ? 9  : $item->status;  ?>
                <td>{{ $item->gainer_primary_user_id == $primaryUserId ? '您已接单' : config('order.status')[$status]  }}</td>
                <td>{{ $item->created_at }}</td>
                <td>
                    <div class="layui-input-inline">
                        <select  lay-filter="operation" data-no="{{ $item->no }}">
                            <option value="">请选择操作</option>
                                @if(in_array($primaryUserId, [$item->gainer_primary_user_id, $item->creator_primary_user_id])
                                || in_array($currentUserId, [$item->gainer_user_id, $item->creator_user_id]))
                                    <option value="detail">订单详情</option>
                                @endif

                                @if(($primaryUserId == $item->creator_primary_user_id || $currentUserId == $item->creator_user_id)
                                  && in_array($item->status, [11]))
                                    <option value="payment">支付订单</option>
                                    <option value="cancel">取消订单</option>
                                @endif

                                @if(($primaryUserId == $item->creator_primary_user_id || $currentUserId == $item->creator_user_id)
                                && in_array($item->status, [1, 5]))
                                    <option value="cancel">取消订单</option>
                                @endif

                                @if(($primaryUserId == $item->creator_primary_user_id || $currentUserId == $item->creator_user_id)
                                && $item->status == 4)
                                    <option value="afterSales">申请售后</option>
                                    <option value="confirm">确认收货</option>
                                @endif

                                @if(($primaryUserId == $item->gainer_primary_user_id || $currentUserId == $item->gainer_user_id) && $item->status == 3)
                                    <option value="delivery">订单发货</option>
                                    <option value="fail">订单失败</option>
                                    <option value="turnBack">返回集市</option>
                                @endif

                                @if(!receivingRecordExist($primaryUserId, $item->no) && $primaryUserId != $item->creator_primary_user_id)
                                    <option value="receiving">立即接单</option>
                                @endif
                        </select>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="10">没有数据</td></tr>
        @endforelse
        </tbody>
    </table>
{!! $orders->appends(['type' => $type,'no' => $no ])->render() !!}