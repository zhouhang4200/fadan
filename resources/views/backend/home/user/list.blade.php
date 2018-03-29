<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>用户ID</th>
            <th>用户账号</th>
            <th>拥有角色</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        @forelse($user->newRoles as $role)
                        【{{ $role->alias }}】
                        @empty
                        --
                        @endforelse
                    </td>
                    <td style="text-align: center">
                        <button lay-id="{{ $user->id }}" lay-name="{{ $user->name }}" lay-role-ids="{{ implode($user->newRoles()->pluck('new_roles.id')->toArray(), '-') ?? '' }}" lay-submit="" class="layui-btn layui-btn-normal" lay-filter="match">设置角色</button>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</form>
    {{ $users->links() }}