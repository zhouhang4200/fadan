<form class="layui-form" id="goods-form">
    <div class="layui-form-item">
        <div class="layui-input-inline">
            <select name="goods_id"   lay-search="">
                <option value="">所有商品</option>
                @foreach ($goods as $key => $value)
                    <option value="{{ $key }}" {{ $key == $goodsId ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="other_user_id" placeholder="用户ID" value="{{ $otherUserId  }}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="category-search">查询</button>
        </div>
        <button class="layui-btn layui-btn-normal fr" data-type="{{ $type }}" lay-submit lay-filter="goods-add">添加用户ID</button>
    </div>
</form>

<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th width="6%">ID</th>
        <th>类型</th>
        <th>商品</th>
        <th>用户ID</th>
        <th>备注</th>
        <th>添加时间</th>
        <th>更新时间</th>
        <th width="7%">操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($controlCategoryList as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->type == 1 ? '白名单' : '黑名单' }}</td>
            <td>{{ $item->goods->name }}</td>
            <td>{{ $item->other_user_id }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->updated_at }}</td>
            <td>
                <button class="layui-btn layui-btn-normal layui-btn-small" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-goods">删除</button>
            </td>
        </tr>
    @empty

        <tr>
            @if($otherUserId  || $goodsId)
                <td colspan="10">没有搜索到相关数据</td>
            @else
                <td colspan="10">您还没有添加{{ $type == 1 ? '白' : '黑' }}名单用户ID</td>
            @endif
        </tr>
    @endforelse
    </tbody>
</table>

{{ $controlCategoryList->appends(['type' => $type, 'goods_od' => $goodsId,'other_user_id' => $otherUserId,])->links() }}