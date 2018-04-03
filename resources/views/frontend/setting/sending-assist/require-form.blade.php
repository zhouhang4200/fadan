<form class="layui-form" method="" action="">
    <table class="layui-table">
        <thead>
        <tr>
            <th style="width:10%">模板名称</th>
            <th style="width:10%">关联游戏</th>
            <th>模板内容</th>
            <th style="width:7%;">设为默认</th>
            <th style="width:15%;">操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orderTemplates as $orderTemplate)
            <tr>
                <td>{{ $orderTemplate->name }}</td>
                <td>{{ $orderTemplate->game->name ?? '' }}</td>
                <td>{{ $orderTemplate->content }}</td>
                <td style="padding-bottom: 0px;padding-top: 0px;">
                    <div class="layui-form-item" pane="" style="width:50px;margin-bottom: 0px;">
                        <div class="layui-input-block" style="margin-left:12px;height:45px;">
                             <input type="checkbox" name="status" lay-filter="default" lay-skin="primary" lay-data="{{ $orderTemplate->id }}" value="1" title="" {{ $orderTemplate->status == 1 ? 'checked' : '' }}>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="text-align: center">
                    <a href="{{ route('frontend.setting.sending-assist.require.edit', ['id' => $orderTemplate->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini">编辑</a>
                    <button class="layui-btn layui-btn-normal layui-btn-mini" lay-submit="" lay-del-id="{{ $orderTemplate->id }}" lay-filter="delete">删除</button>
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
    {{ $orderTemplates->links() }}
</form>