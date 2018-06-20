<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>游戏</th>
            <th>代练类型</th>
            <th>发单价格固定比例</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($datas as $data)
            <tr>
                <td>{{ $data->game_name }}</td>
                <td>{{ $data->game_leveling_type }}</td>
                <td>{{ $data->rebate }} </td>
                <td>
                    <a class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="edit" data-id="{{ $data->id }}" href="{{ route('config.leveling.edit', ['id' => $data->id]) }}">编辑</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="price" data-id="{{ $data->id }}">价格公式</button>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="rebate" data-id="{{ $data->id }}">折扣</button>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $data->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>

