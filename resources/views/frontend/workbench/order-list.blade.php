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
            <th width="13%">操作</th>
        </tr>
        </thead>
        <tbody id="need">
        @forelse($orders as $item)
            <tr>
                <td>{{ $item->no }}</td>
                <td>{{ $item->service_name }}</td>
                <td>{{ $item->game_name }}</td>
                <td>{{ $item->goods_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->amount }}</td>
                <?php $status = receivingRecordExist( $primaryUserId, $item->no) ? 9  : $item->status;  ?>
                <td>{{ config('order.status')[$status] }}</td>
                <td>
                    @if($type == 'need')
                        <div class="layui-input-inline">
                            <select name="city" lay-verify="required" lay-filter="operation">
                                <option value="">请选择操作</option>
                                <option value="0">订单详情</option>
                                <option value="1">订单发货</option>
                                <option value="1">订单失败</option>
                                <option value="2">返回集市</option>
                            </select>
                        </div>
                    @elseif($type == 'ing')
                        <div class="layui-input-inline">
                            <select name="city" lay-verify="required" lay-filter="operation">
                                <option value="">请选择操作</option>
                                <option value="0">订单详情</option>
                            </select>
                        </div>
                    @elseif($type == 'finish')
                        <div class="layui-input-inline">
                            <select name="city" lay-verify="required" lay-filter="operation">
                                <option value="">请选择操作</option>
                                <option value="0">订单详情</option>
                                <option value="1">确认收货</option>
                            </select>
                        </div>
                    @elseif($type == 'after-sales')
                        <div class="layui-input-inline">
                            <select name="city" lay-verify="required" lay-filter="operation">
                                <option value="">请选择操作</option>
                                <option value="0">订单详情</option>
                                <option value="1">确认收货</option>
                            </select>
                        </div>
                    @elseif($type == 'market')
                        <div class="layui-input-inline">
                            <select name="city" lay-verify="required" lay-filter="operation" data-no="{{ $item->no }}">
                                <option value="">请选择操作</option>
                                @if($item->creator_user_id == $currentUserId || $item->creator_primary_user_id == $primaryUserId)
                                    <option value="0">取消订单</option>
                                @elseif (!receivingRecordExist( $primaryUserId, $item->no))
                                    <option value="receiving">立即接单</option>
                                @endif
                            </select>
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="10">没有数据</td></tr>
        @endforelse
        </tbody>
    </table>
{!! $orders->appends(['type' => $type,'no' => $no ])->render() !!}