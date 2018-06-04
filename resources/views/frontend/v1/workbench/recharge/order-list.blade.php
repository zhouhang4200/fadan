<?php
// 获取当前用户ID
$currentUserId = Auth::user()->id;
// 获取主账号
$primaryUserId = Auth::user()->getPrimaryUserId();
?>
<div class="search" style="float: right;margin: 8px 0">
    <div class="layui-inline">
        <select name="search_type" lay-verify="">
            <option value="">请选择搜索类型</option>
            <option value="1" @if($searchType == 1) selected  @endif>千手订单号</option>
            <option value="2" @if($searchType == 2) selected  @endif>外部订单号</option>
            <option value="3" @if($searchType == 3) selected  @endif>账号</option>
        </select>
    </div>
    <div class="layui-inline">
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="search_content" autocomplete="off" class="layui-input" lay-verify="" value="{{ $searchContent }}">
        </div>
    </div>
    <div class="layui-inline">
        <button class="qs-btn" lay-submit="" lay-filter="search">搜索</button>
    </div>
</div>
<table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>订单号</th>
            <th>旺旺号</th>
            <th>类型</th>
            <th>游戏</th>
            <th>商品</th>
            <th>账号</th>
            <th>数量</th>
            <th>单价</th>
            <th>总价</th>
            <th>状态</th>
            @if ($type == 'market' || $type == 'need')
            <th>倒计时</th>
            @endif
            <th>下单时间</th>
            <th width="13%">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $item)
            <?php $detail = $item->detail->pluck('field_value', 'field_name')->toArray() ?>
            <tr data-no="{{ $item->no }}">
                <td>千手：{{ $item->no }}<br>外部：{{ $item->foreign_order_no }}</td>
                <td>{{ $item->wang_wang ?: '' }}</td>
                <td>{{ $item->service_name }}</td>
                <td>{{ $item->game_name }}</td>
                <td>{{ $item->goods_name }}</td>
                <td>{{ $detail['account'] ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->amount }}</td>
                <?php $status = receivingRecordExist( $primaryUserId, $item->no) && $item->status == 1 ? 9  : $item->status;  ?>
                <td>{{ ($item->gainer_primary_user_id == $primaryUserId && $type == 'need') ? '您已接单' : config('order.status')[$status]  }}</td>
                @if ($type == 'market' || $type == 'need')
                <td class="end-time" data-time="{{ $item->created_at }}"></td>
                @endif
                <td >{{ $item->created_at }}</td>
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
            <tr><td colspan="999">没有数据</td></tr>
        @endforelse
        </tbody>
    </table>
{!! $orders->appends(['type' => $type,'no' => $no ])->render() !!}