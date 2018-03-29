<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>名称</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($modules as $module)
                <tr>
                    <td>{{ $module->name }}</td>
                    <td style="text-align: center">
                        <button lay-id="{{ $module->id }}" lay-name="{{ $module->name }}" lay-submit="" class="layui-btn layui-btn-normal" lay-filter="edit">编缉</button>
                        <button class="layui-btn layui-btn-normal" lay-filter="destroy" lay-submit=""  lay-id="{{ $module->id }}">删除</button>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</form>