<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>序号</th>
            <th>代练层级</th>
            <th>到下一层级价格</th>
            <th>到下一层级耗时</th>
            <th>该层级安全保证金</th>
            <th>该层级效率保证金</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($datas as $data)
            <tr>
                <td>{{ $data->game_leveling_number+0 }}</td>
                <td>{{ $data->game_leveling_level }}</td>
                <td>{{ $data->level_price+0 }} </td>
                <td>{{ $data->level_hour+0 }} </td>
                <td>{{ $data->level_security_deposit+0 }} </td>
                <td>{{ $data->level_efficiency_deposit+0 }} </td>
                <td>
                    <a class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="edit" data-id="{{ $data->id }}" href="{{ route('config.leveling.price.edit', ['id' => $data->id]) }}" data-game-id="{{ $data->game_id }}" data-game-name="{{ $data->game_name }}" data-type="{{ $data->game_leveling_type }}">编辑</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $data->id }}" data-game-id="{{ $data->game_id }}" data-game-name="{{ $data->game_name }}" data-type="{{ $data->game_leveling_type }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>

