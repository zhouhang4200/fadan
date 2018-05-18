<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm" style="text-align:center;">
        <thead>
        <tr>
            <th>打手昵称</th>
            <th>电话</th>
            <th>QQ</th>
            <th>备注</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        <tbody>
            @forelse($hatchetManBlacklists as $hatchetManBlacklist)
                <tr>
                    <td>{{ $hatchetManBlacklist->hatchet_man_name }}</td>
                    <td>{{ $hatchetManBlacklist->hatchet_man_phone }}</td>
                    <td>{{ $hatchetManBlacklist->hatchet_man_qq }}</td>
                    <td>{{ $hatchetManBlacklist->content }}</td>
                    <td>
                    <a class="qs-btn qs-btn-normal qs-btn-mini" href="{{ route('hatchet-man-blacklist.edit', ['id' => $hatchetManBlacklist->id]) }}">编辑</a>
                    <button class="qs-btn qs-btn-normal qs-btn-mini" lay-submit="" lay-filter="delete" lay-data="{{ $hatchetManBlacklist->id }}">删除</button>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</form>
