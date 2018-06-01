<form class="layui-form" method="" action="" lay-filter="set">
    <table class="layui-table">
        <thead>
        <tr>
            <th style="width:10%">游戏</th>
            <th>发布渠道</th>
        </tr>
        </thead>
        <tbody>
        @forelse($games as $gameId => $gameName)
            <?php $orderSendChannel = \App\Models\OrderSendChannel::where('user_id', $primaryUserId)->where('game_id', $gameId)->first(); ?>
            <tr>
                <td>{{ $gameName }}</td>
                <td>
                    <div class="layui-input-inline">
                        <input type="checkbox" lay-game-name="{{ $gameName }}" lay-filter="set" lay-game-id="{{ $gameId }}" {{ isset($orderSendChannel) && in_array(1, explode('-', $orderSendChannel->third)) && $orderSendChannel->game_id == $gameId ? '' : 'checked' }} name="third{{ $gameId }}" lay-skin="primary" title="show91平台" value="1">
                        <input type="checkbox" lay-game-name="{{ $gameName }}" lay-filter="set" lay-game-id="{{ $gameId }}" {{ isset($orderSendChannel) && in_array(4, explode('-', $orderSendChannel->third)) && $orderSendChannel->game_id == $gameId ? '' : 'checked' }} name="third{{ $gameId }}" lay-skin="primary" title="dd373平台" value="4">
                        <input type="checkbox" lay-game-name="{{ $gameName }}" lay-filter="set" lay-game-id="{{ $gameId }}" {{ isset($orderSendChannel) && in_array(3, explode('-', $orderSendChannel->third)) && $orderSendChannel->game_id == $gameId ? '' : 'checked' }} name="third{{ $gameId }}" lay-skin="primary" title="蚂蚁代练" value="3">
                        <input type="checkbox" lay-game-name="{{ $gameName }}" lay-filter="set" lay-game-id="{{ $gameId }}" {{ isset($orderSendChannel) && in_array(5, explode('-', $orderSendChannel->third)) && $orderSendChannel->game_id == $gameId ? '' : 'checked' }} name="third{{ $gameId }}" lay-skin="primary" title="丸子代练" value="5">
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
</form>