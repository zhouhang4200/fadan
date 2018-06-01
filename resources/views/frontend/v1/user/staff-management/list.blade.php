<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm" style="text-align:center;">
        <thead>
        <tr>
            <th>编号</th>
            <th>员工姓名</th>
            <th>账号</th>
            <th>代练类型</th>
            <th>岗位</th>
            <th>QQ</th>
            <th>微信</th>
            <th>电话</th>
            <th>最后操作时间</th>
            <th>备注</th>
            <th>状态</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ config('user.leveling_type')[$user->leveling_type] }}</td>
                    <td>{{ $user->newRoles->pluck('alias')->count() > 0 ? implode(' |
                    ', $user->newRoles->pluck('alias')->toArray()) : '--' }}</td>
                    <td>{{ $user->qq ?? '--' }}</td>
                    <td>{{ $user->wechat ?? '--' }}</td>
                    <td>{{ $user->phone ?? '--' }}</td>
                    <td>{{ $user->updated_at ?? '--' }}</td>
                    <td>{{ $user->remark ?? '--' }}</td>
                    <td><input type="checkbox" name="open" lay-data="{{ $user->id }}" {{ $user->status == 0 ? 'checked' : '' }} lay-skin="switch" lay-filter="open" ></td>
                    <td>
                    @if(! $user->deleted_at)
                        <a class="qs-btn layui-btn-normal layui-btn-mini" href="{{ route('staff-management.edit', ['id' => $user->id]) }}">编辑</a>
                        <button class="qs-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-filter="delete" lay-data="{{ $user->id }}">删除</button>
                    @else
                        --
                    @endif
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</form>
