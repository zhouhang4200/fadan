<form class="layui-form" id="user-form">
    <div class="layui-form-item">
        <div class="layui-input-inline" style="width: 200px;">
            <input type="text" class="layui-input" name="other_user_id" placeholder="用户ID" value="{{ $otherUserId  }}">
        </div>
        <div class="layui-input-inline" style="width: 200px;">
            <button class="layui-btn layui-btn-normal" type="submit" lay-submit="" lay-filter="user-search">查询</button>
        </div>
        <button class="layui-btn layui-btn-normal fr"  data-type="{{ $type }}" lay-submit lay-filter="user-add">添加用户ID</button>
    </div>
</form>

<table class="layui-table" lay-size="sm">
    <thead>
    <tr>
        <th width="6%">ID</th>
        <th>用户ID</th>
        <th>备注</th>
        <th width="16%">添加时间</th>
        <th width="16%">更新时间</th>
        <th width="8%">操作</th>
    </tr>
    </thead>
    <tbody>
    @forelse($controlUserList as $item)
        <tr data-id="{{ $item->id }}">
            <td>{{ $item->id }}</td>
            <td>{{ $item->other_user_id }}</td>
            <td>{{ $item->remark }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->updated_at }}</td>
            <td>
                <button class="layui-btn layui-btn-normal layui-btn-small delete" data-id="{{ $item->id }}" lay-submit="" lay-filter="delete-user">删除</button>
            </td>
        </tr>
    @empty
        <tr>
            @if($otherUserId)
                <td colspan="10">没有搜索到相关数据</td>
            @else
                <td colspan="10">您还没有添加{{ $type == 1 ? '白' : '黑' }}名单用户ID</td>
            @endif
        </tr>
    @endforelse
    </tbody>
</table>

{{ $controlUserList->appends(['type' => $type, 'other_user_id' => $otherUserId])->links() }}