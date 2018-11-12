<?php

Route::prefix('v1')->namespace('V1')->group(function($router) {
//    Route::prefix('auth')->group(function($router) {
//        $router->post('login', 'Auth\AuthController@login');
//        $router->post('logout', 'OpenApi\Auth\AuthController@logout');
//    });

    Route::middleware('open.api')->group(function($router) {

        # 游戏
        Route::namespace('Game')->group(function($router) {
            # 获取所有游戏
            $router->post('games','IndexController@index');
            # 根据指定游戏ID获取所有区
            $router->post('regions','IndexController@index');
            # 根据指定游戏ID获取所有区
            $router->post('servers','IndexController@index');
        });

        # 订单
        Route::prefix('order')->namespace('Order')->group(function($router) {

            # 游戏代练
            Route::prefix('game-leveling')->group(function($router) {
                # 下单
                $router->post('create','GameLevelingOrderController@create');
                # 查看订单
                $router->post('show','GameLevelingOrderController@show');
                # 更新订单
                $router->post('update','GameLevelingOrderController@update');
                # 下架
                $router->post('off-sale','GameLevelingOrderController@offSale');
                # 上架
                $router->post('on-sale','GameLevelingOrderController@onSale');
                # 撤单
                $router->post('delete','GameLevelingOrderController@delete');
                # 撤销
                $router->post('apply-consult','GameLevelingOrderController@applyConsult');
                # 取消撤销
                $router->post('cancel-consult','GameLevelingOrderController@cancelConsult');
                # 同意撤销
                $router->post('agree-consult','GameLevelingOrderController@agreeConsult');
                # 拒绝撤销
                $router->post('reject-consult','GameLevelingOrderController@rejectConsult');
                # 申请仲裁
                $router->post('apply-complain','GameLevelingOrderController@applyComplain');
                # 取消仲裁
                $router->post('cancel-complain','GameLevelingOrderController@cancelComplain');
                # 锁定
                $router->post('lock','GameLevelingOrderController@lock');
                # 取消锁定
                $router->post('cancel-lock','GameLevelingOrderController@cancelLock');
            });
        });
    });

});