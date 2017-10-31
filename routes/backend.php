<?php

Route::namespace('Backend\Auth')->group(function () {
    // 登录
    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->name('admin.logout');
    // 注册
    // Route::get('register', 'RegisterController@showRegistrationForm')->name('admin.register');
    // Route::post('register', 'RegisterController@register');
    // 密码找回
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');
});


Route::middleware(['auth:admin'])->namespace('Backend')->group(function () {

    Route::get('/', 'DashboardController@index')->name('dashboard');
    // 系统日志
    // Route::resource('system-logs', 'SystemLogController', ['only' => ['index']]);
    Route::get('system-logs', 'SystemLogController@index')->name('system-logs.index');

    Route::namespace('Goods')->prefix('goods')->group(function (){

        // 服务
        Route::prefix('service')->group(function (){
            // 列表
            Route::get('/', 'ServiceController@index')->name('goods.service.index');
            // 查看
            Route::get('/{id}', 'ServiceController@show')->name('goods.service.show');
            // 修改
            Route::post('edit', 'ServiceController@edit')->name('goods.service.edit');
            // 保存
            Route::post('store', 'ServiceController@store')->name('goods.service.store');
            // 设置分类状态
            Route::post('status', 'ServiceController@status')->name('goods.service.status');
        });
        // 游戏
        Route::prefix('game')->group(function (){
            // 列表
            Route::get('/', 'GameController@index')->name('goods.game.index');
            // 查看
            Route::get('/{id}', 'GameController@show')->name('goods.game.show');
            // 修改
            Route::post('edit', 'GameController@edit')->name('goods.game.edit');
            // 保存
            Route::post('store', 'GameController@store')->name('goods.game.store');
            // 设置分类状态
            Route::post('status', 'GameController@status')->name('goods.game.status');
        });
        // 模版
        Route::prefix('template')->group(function (){
            // 列表
            Route::get('/', 'TemplateController@index')->name('goods.template.index');
            // 创建视图
            Route::get('create', 'TemplateController@index')->name('goods.template.created');
            // 保存
            Route::post('/', 'TemplateController@store')->name('goods.template.store');
            // 查看
            Route::get('/{templateId}', 'TemplateController@show')->name('goods.template.show');
            // 配置
            Route::get('config/{templateId}', 'TemplateController@config')->name('goods.template.config');
            // 删除
            Route::post('destroy{templateId}', 'TemplateController@destroy')->name('goods.template.destroy');
            // 设置分类状态
            Route::post('status', 'TemplateController@status')->name('goods.template.status');
            // 保存修改
            Route::post('edit', 'TemplateController@edit')->name('goods.template.edit');

            Route::prefix('widget')->group(function (){
                // 获取指定组件
                Route::post('show', 'TemplateWidgetController@show')->name('goods.template.widget.show');
                // 获取指定模版ID所有 select 组件
                Route::post('show-select-all', 'TemplateWidgetController@showSelectWidgetByGoodsTemplateId')->name('goods.template.widget.show-select-all');
                // 获取指定父级ID组件 的值
                Route::post('show-select-value', 'TemplateWidgetController@showSelectValueByParentId')->name('goods.template.widget.show-select-value');
                // 保存修改
                Route::post('edit', 'TemplateWidgetController@edit')->name('goods.template.widget.edit');
                // 获取模版所有组件
                Route::get('all/{templateId}', 'TemplateWidgetController@showAll')->name('goods.template.widget.show.all');
                // 保存
                Route::post('/', 'TemplateWidgetController@store')->name('goods.template.widget.store');
                // 删除
                Route::post('destroy', 'TemplateWidgetController@destroy')->name('goods.template.widget.destroy');
            });
        });
    });

    // 用户
    Route::namespace('User')->prefix('user')->group(function (){
        // 用户账号列表
        Route::namespace('Frontend')->prefix('frontend')->group(function () {
            Route::get('/', 'UserController@index')->name('frontend.user.index');
            // 手动加款
            Route::post('recharge', 'UserController@recharge')->name('frontend.user.recharge');
        });
        // 后台账号列表
        Route::namespace('Frontend')->prefix('backend')->group(function () {
            Route::get('/', 'UserController@index')->name('backend.user.index');
        });
    });


    Route::namespace('Rbac')->prefix('rbac')->group(function () {
        // 前台角色
        // Route::resource('roles', 'RoleController', ['except' => ['show']]);
        Route::get('roles', 'RoleController@index')->name('roles.index');
        Route::get('roles/create', 'RoleController@create')->name('roles.create');
        Route::post('roles', 'RoleController@store')->name('roles.store');
        Route::get('roles/{id}/edit', 'RoleController@edit')->name('roles.edit');
        Route::put('roles/{id}', 'RoleController@update')->name('roles.update');
        Route::delete('roles/{id}', 'RoleController@destroy')->name('roles.destroy');
        // 后台角色
        // Route::resource('admin-roles', 'AdminRoleController', ['except' => ['show']]);
        Route::get('admin-roles', 'AdminRoleController@index')->name('admin-roles.index');
        Route::get('admin-roles/create', 'AdminRoleController@create')->name('admin-roles.create');
        Route::post('admin-roles', 'AdminRoleController@store')->name('admin-roles.store');
        Route::get('admin-roles/{id}/edit', 'AdminRoleController@edit')->name('admin-roles.edit');
        Route::put('admin-roles/{id}', 'AdminRoleController@update')->name('admin-roles.update');
        Route::delete('admin-roles/{id}', 'AdminRoleController@destroy')->name('admin-roles.destroy');
        // 前台权限
        // Route::resource('permissions', 'PermissionController', ['except' => ['show']]);
        Route::get('permissions', 'PermissionController@index')->name('permissions.index');
        Route::get('permissions/create', 'PermissionController@create')->name('permissions.create');
        Route::post('permissions', 'PermissionController@store')->name('permissions.store');
        Route::get('permissions/{id}/edit', 'PermissionController@edit')->name('permissions.edit');
        Route::put('permissions/{id}', 'PermissionController@update')->name('permissions.update');
        Route::delete('permissions/{id}', 'PermissionController@destroy')->name('permissions.destroy');
        // 后台权限
        // Route::resource('admin-permissions', 'AdminPermissionController', ['except' => ['show']]);
        Route::get('admin-permissions', 'AdminPermissionController@index')->name('admin-permissions.index');
        Route::get('admin-permissions/create', 'AdminPermissionController@create')->name('admin-permissions.create');
        Route::post('admin-permissions', 'AdminPermissionController@store')->name('admin-permissions.store');
        Route::get('admin-permissions/{id}/edit', 'AdminPermissionController@edit')->name('admin-permissions.edit');
        Route::put('admin-permissions/{id}', 'AdminPermissionController@update')->name('admin-permissions.update');
        Route::delete('admin-permissions/{id}', 'AdminPermissionController@destroy')->name('admin-permissions.destroy');
        // 前台账号分配角色
        // Route::resource('groups', 'GroupController');
        Route::get('groups', 'GroupController@index')->name('groups.index');
        Route::get('groups/create', 'GroupController@create')->name('groups.create');
        Route::post('groups', 'GroupController@store')->name('groups.store');
        Route::get('groups/{id}/edit', 'GroupController@edit')->name('groups.edit');
        Route::get('groups/{id}', 'GroupController@show')->name('groups.show');
        Route::put('groups/{id}', 'GroupController@update')->name('groups.update');
        Route::delete('groups/{id}', 'GroupController@destroy')->name('groups.destroy');
        // 后台账号分配角色
        // Route::resource('admin-groups', 'AdminGroupController');
        Route::get('admin-groups', 'AdminGroupController@index')->name('admin-groups.index');
        Route::get('admin-groups/create', 'AdminGroupController@create')->name('admin-groups.create');
        Route::post('admin-groups', 'AdminGroupController@store')->name('admin-groups.store');
        Route::get('admin-groups/{id}/edit', 'AdminGroupController@edit')->name('admin-groups.edit');
        Route::get('admin-groups/{id}', 'AdminGroupController@show')->name('admin-groups.show');
        Route::put('admin-groups/{id}', 'AdminGroupController@update')->name('admin-groups.update');
        Route::delete('admin-groups/{id}', 'AdminGroupController@destroy')->name('admin-groups.destroy');
        // 前台模块
        // Route::resource('modules', 'ModuleController', ['except' => ['show']]);
        Route::get('modules', 'ModuleController@index')->name('modules.index');
        Route::get('modules/create', 'ModuleController@create')->name('modules.create');
        Route::post('modules', 'ModuleController@store')->name('modules.store');
        Route::get('modules/{id}/edit', 'ModuleController@edit')->name('modules.edit');
        Route::put('modules/{id}', 'ModuleController@update')->name('modules.update');
        Route::delete('modules/{id}', 'ModuleController@destroy')->name('modules.destroy');
        // 后台模块
        // Route::resource('admin-modules', 'AdminModuleController', ['except' => ['show']]);
        Route::get('admin-modules', 'AdminModuleController@index')->name('admin-modules.index');
        Route::get('admin-modules/create', 'AdminModuleController@create')->name('admin-modules.create');
        Route::post('admin-modules', 'AdminModuleController@store')->name('admin-modules.store');
        Route::get('admin-modules/{id}/edit', 'AdminModuleController@edit')->name('admin-modules.edit');
        Route::put('admin-modules/{id}', 'AdminModuleController@update')->name('admin-modules.update');
        Route::delete('admin-modules/{id}', 'AdminModuleController@destroy')->name('admin-modules.destroy');

        Route::get('accounts', 'AccountController@index')->name('accounts.index');
        // 后台账号
        Route::get('admin-accounts', 'AdminAccountController@index')->name('admin-accounts.index');
    });

    Route::namespace('Account')->prefix('account')->group(function () {
        // 账号管理-我的账号
        Route::get('login-history', 'LoginRecordController@index')->name('login-record.index');
        // 实名认证 - 通过
        Route::post('pass', 'PassOrRefuseController@pass')->name('pass-or-refuse.pass');
        // 实名认证 - 拒绝
        Route::post('refuse', 'PassOrRefuseController@refuse')->name('pass-or-refuse.refuse');
        // 实名认证
        // Route::resource('admin-idents', 'AdminIdentController', ['only' => ['index', 'show']]);
        Route::get('admin-idents', 'AdminIdentController@index')->name('admin-idents.index');
        Route::get('admin-idents/{id}', 'AdminIdentController@show')->name('admin-idents.show');

    });

    // 订单
    Route::group([], function () {
        // 订单列表
        // Route::resource('orders', 'OrderController', ['only' => ['index', 'show']]);
        Route::get('orders', 'OrderController@index')->name('orders.index');
        Route::get('orders/{id}', 'OrderController@show')->name('orders.show');
    });

    // 财务
    Route::namespace('Finance')->prefix('finance')->group(function () {
        Route::get('platform-asset', 'PlatformAssetController@index')->name('finance.platform-asset');

        Route::get('platform-amount-flow', 'PlatformAmountFlowController@index')->name('finance.platform-amount-flow');
        Route::get('platform-amount-flow/export', 'PlatformAmountFlowController@export')->name('finance.platform-amount-flow.export');

        Route::get('platform-asset-daily', 'PlatformAssetDailyController@index')->name('finance.platform-asset-daily');
        Route::get('platform-asset-daily/export', 'PlatformAssetDailyController@export')->name('finance.platform-asset-daily.export');

        Route::get('user-asset', 'UserAssetController@index')->name('finance.user-asset');

        Route::get('user-asset-daily', 'UserAssetDailyController@index')->name('finance.user-asset-daily');

        Route::get('user-amount-flow', 'UserAmountFlowController@index')->name('finance.user-amount-flow');

        Route::get('user-widthdraw-order', 'UserWithdrawOrderController@index')->name('finance.user-widthdraw-order');
        Route::post('user-widthdraw-order/complete/{userWithdrawOrder}', 'UserWithdrawOrderController@complete')->name('finance.user-widthdraw-order.complete');
        Route::post('user-widthdraw-order/refuse/{userWithdrawOrder}', 'UserWithdrawOrderController@refuse')->name('finance.user-widthdraw-order.refuse');
    });

    Route::prefix('template')->group(function (){
        Route::get('form', 'TemplateController@form')->name('template.form');
        Route::get('icons1', 'TemplateController@icons1')->name('template.icons1');
        Route::get('icons2', 'TemplateController@icons2')->name('template.icons2');
    });
});
