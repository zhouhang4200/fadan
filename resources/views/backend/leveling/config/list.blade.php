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
                    <a class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="price" href="{{ route('config.leveling.price.index', ['game_id' => $data->game_id, 'game_name' => $data->game_name, 'type' => $data->game_leveling_type]) }}" data-game-id="{{ $data->game_id }}" data-game-name="{{ $data->game_name }}" data-type="{{ $data->game_leveling_type }}" data-id="{{ $data->id }}">价格公式</a>
                    <a class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="rebate" data-game-id="{{ $data->game_id }}" data-game-name="{{ $data->game_name }}" data-type="{{ $data->game_leveling_type }}" href="{{ route('config.leveling.rebate.index', ['game_id' => $data->game_id, 'game_name' => $data->game_name, 'type' => $data->game_leveling_type]) }}" data-id="{{ $data->id }}">折扣</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $data->id }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>

