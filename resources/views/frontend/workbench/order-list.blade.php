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
                <td>{{ $item->status }}</td>
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
                            <select name="city" lay-verify="required" lay-filter="operation">
                                <option value="">请选择操作</option>
                                <option value="0">取消订单</option>
                                <option value="1">订单发货</option>
                                <option value="1">订单失败</option>
                                <option value="2">返回集市</option>
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