<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>模块名</th>
            <th>权限名</th>
            <th>权限别名</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($permissions as $permission)
                <tr>
                    <td>{{ $permission->newModule->name }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->alias }}</td>
                    <td style="text-align: center">
                        <button lay-id="{{ $permission->id }}" lay-name="{{ $permission->name }}" lay-alias="{{ $permission->alias }}" lay-module_id="{{ $permission->new_module_id }}" lay-submit="" class="layui-btn layui-btn-normal" lay-filter="edit">编缉</button>
                        <button class="layui-btn layui-btn-normal" lay-filter="destroy" lay-submit=""  lay-id="{{ $permission->id }}">删除</button>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
    {{ $permissions->links() }}
</form>