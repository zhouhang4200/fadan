<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>提升层级</th>
            <th>代练价格折扣</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($datas as $data)
            <tr>
                <td>{{ $data->level_count+0 }}</td>
                <td>{{ $data->rebate+0 }}</td>
                <td>
                    <a class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="edit" data-id="{{ $data->id }}" href="{{ route('config.leveling.rebate.edit', ['id' => $data->id]) }}" data-game-id="{{ $data->game_id }}" data-game-name="{{ $data->game_name }}" data-type="{{ $data->game_leveling_type }}">编辑</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $data->id }}" data-game-id="{{ $data->game_id }}" data-game-name="{{ $data->game_name }}" data-type="{{ $data->game_leveling_type }}">删除</button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>

