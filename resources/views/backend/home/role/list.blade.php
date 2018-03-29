<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>角色名</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td>
                    {{ $role->alias }}
                </td>
                <td style="text-align: center">
                    <a href="{{ route('home.role.edit', ['id' => $role->id]) }}" class="layui-btn layui-btn-normal" lay-filter="edit">编缉</a>
                    <button class="layui-btn layui-btn-normal" lay-filter="destroy" lay-submit=""  lay-id="{{ $role->id }}">删除</button>
                </td>
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</form>