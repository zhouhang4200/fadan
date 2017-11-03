<form class="layui-form" id="search-form">
    <div class="layui-form-item">
        <div class="layui-input-inline">
            <select name="service_id">
                <option value="">所有类型</option>
                @foreach ($services as $key => $value)
                    <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-input-inline">
            <select name="game_id">
                <option value="">所有游戏</option>
                @foreach ($games as $key => $value)
                    <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="other_user_id" placeholder="用户ID" value="{{ $otherUserId  }}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
        </div>
        <a  href="{{ route('frontend.goods.create') }}" class="layui-btn layui-btn-normal fr" >添加用户ID</a>
    </div>
</form>

<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th width="6%">ID</th>
        <th>用户ID</th>
        <th>备注</th>
        <th>添加时间</th>
        <th>更新时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($controlUserList as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->other_user_id }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->updated_at }}</td>
            <td><button class="layui-btn layui-btn-normal layui-btn-small edit"><a href="{{ route('frontend.goods.edit', ['id' => $item->id]) }}" style="color: #fff">编辑</a></button>
                <button class="layui-btn layui-btn-normal layui-btn-small delete" onclick="deletes({{ $item->id }})">删除</button></td>
        </tr>
    @empty
        <tr>
            <td colspan="10">您还没有添加{{ $type == 1 ? '白' : '黑' }}名单用户ID</td>
        </tr>
    @endforelse
    </tbody>
</table>

{{ $controlUserList->appends([
'type' => $type,
'other_user_id' => $otherUserId,
])->links() }}