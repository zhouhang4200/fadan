<form class="layui-form" id="goods-form">
    <div class="layui-form-item">
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="foreign_goods_id" placeholder="淘宝商品ID" value="{{ $foreignGoodsId  }}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="category-search">查询</button>
        </div>
        <button class="layui-btn layui-btn-normal fr" lay-submit lay-filter="goods-add">添加商品</button>
    </div>
</form>

<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th>服务类型</th>
        <th>淘宝商品ID</th>
        <th>备注</th>
        <th>状态</th>
        <th>添加时间</th>
        <th>更新时间</th>
        <th width="7%">操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($automaticallyGrabGoods as $item)
        <tr>
            <td>{{ $item->service_id }}</td>
            <td>{{ $item->foreign_goods_id }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->updated_at }}</td>
            <td>
                <button class="layui-btn layui-btn-normal layui-btn-small" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
            </td>
        </tr>
    @empty

    @endforelse
    </tbody>
</table>

{{ $automaticallyGrabGoods->links() }}