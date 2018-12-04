<?php
# 渠道pc端
Route::namespace('Channel')->group(function () {
    Route::middleware(['channel.user'])->group(function () {
        #　视图挂载页
        Route::get('/{vue?}', function (){
            return view('channel.pc');
        })->where('vue', '[\/\w\.-]*');
        #　获取代练游戏
        Route::post('games', 'GameLevelingChannelOrderController@games');
        #　获取游戏区与服
        Route::post('game-region-server', 'GameLevelingChannelOrderController@regionServer');
        # 获取代练类型
        Route::post('game-leveling-types', 'GameLevelingChannelOrderController@gameLevelingTypes');
        # 获取代练等级
        Route::post('game-leveling-levels', 'GameLevelingChannelOrderController@gameLevelingLevels');
        # 计算代练价格和时间
        Route::post('game-leveling-amount-time', 'GameLevelingChannelOrderController@gameLevelingAmountTime');
        # 创建订单
        Route::post('store', 'GameLevelingChannelOrderController@pcStore');
        # 详情页
        Route::post('show', 'GameLevelingChannelOrderController@show')->name('channel.show');
        # 渠道订单列表
        Route::post('order-list', 'GameLevelingChannelOrderController@orderList')->name('channel.order-list');
        # 确认收货
        Route::post('complete', 'GameLevelingChannelOrderController@complete')->name('channel.complete');
        # 申请退款
        Route::post('apply-refund', 'GameLevelingChannelOrderController@applyRefund')->name('channel.apply-refund');
        #　取消退款
        Route::post('cancel-refund', 'GameLevelingChannelOrderController@cancelRefund')->name('channel.cancel-refund');
    });
    # 微信支付回调
    Route::any('pay/wx/notify', 'GameLevelingChannelOrderController@weChatNotify')->name('channel.game-leveling.wx.pay.notify');
});