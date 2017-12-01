<?php

Route::namespace('Backend\Auth')->group(function () {
    // 登录
    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login')->name('admin.post.login');
    Route::post('logout', 'LoginController@logout')->name('admin.logout');
    // 注册
    Route::get('register', 'RegisterController@showRegistrationForm')->name('admin.register');
    Route::post('register', 'RegisterController@register');
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
    Route::get('system-logs', 'SystemLogController@index')->name('system-logs.index')->middleware('permission:system-logs.index');

    Route::namespace('Goods')->prefix('goods')->group(function (){

        // 服务
        Route::prefix('service')->group(function (){
            // 列表
            Route::get('/', 'ServiceController@index')->name('goods.service.index')->middleware('permission:goods.service.index');
            // 查看
            Route::get('/{id}', 'ServiceController@show')->name('goods.service.show')->middleware('permission:goods.service.index');
            // 修改
            Route::post('edit', 'ServiceController@edit')->name('goods.service.edit')->middleware('permission:goods.service.show');
            // 保存
            Route::post('store', 'ServiceController@store')->name('goods.service.store')->middleware('permission:goods.service.store');
            // 设置分类状态
            Route::post('status', 'ServiceController@status')->name('goods.service.status')->middleware('permission:goods.service.status');
        });
        // 游戏
        Route::prefix('game')->group(function (){
            // 列表
            Route::get('/', 'GameController@index')->name('goods.game.index')->middleware('permission:goods.game.index');
            // 查看
            Route::get('/{id}', 'GameController@show')->name('goods.game.show')->middleware('permission:goods.game.show');
            // 修改
            Route::post('edit', 'GameController@edit')->name('goods.game.edit')->middleware('permission:goods.game.edit');
            // 保存
            Route::post('store', 'GameController@store')->name('goods.game.store')->middleware('permission:goods.game.store');
            // 设置分类状态
            Route::post('status', 'GameController@status')->name('goods.game.status')->middleware('permission:goods.game.status');
        });
        // 模版
        Route::prefix('template')->group(function (){
            // 列表
            Route::get('/', 'TemplateController@index')->name('goods.template.index')->middleware('permission:goods.template.index');
            // 创建视图
            Route::get('create', 'TemplateController@index')->name('goods.template.created')->middleware('permission:goods.template.created');
            // 保存
            Route::post('/', 'TemplateController@store')->name('goods.template.store')->middleware('permission:goods.template.store');
            // 查看
            Route::get('/{templateId}', 'TemplateController@show')->name('goods.template.show')->middleware('permission:goods.template.show');
            // 配置
            Route::get('config/{templateId}', 'TemplateController@config')->name('goods.template.config')->middleware('permission:goods.template.config');
            // 删除
            Route::post('destroy{templateId}', 'TemplateController@destroy')->name('goods.template.destroy')->middleware('permission:goods.template.destroy');
            // 设置分类状态
            Route::post('status', 'TemplateController@status')->name('goods.template.status')->middleware('permission:goods.template.status');
            // 保存修改
            Route::post('edit', 'TemplateController@edit')->name('goods.template.edit')->middleware('permission:goods.template.edit');

            Route::prefix('widget')->group(function (){
                // 获取指定组件
                Route::post('show', 'TemplateWidgetController@show')->name('goods.template.widget.show')->middleware('permission:goods.template.widget.show');
                // 获取指定模版ID所有 select 组件
                Route::post('show-select-all', 'TemplateWidgetController@showSelectWidgetByGoodsTemplateId')->name('goods.template.widget.show-select-all')->middleware('permission:goods.template.widget.show-select-all');
                // 获取指定父级ID组件 的值
                Route::post('show-select-value', 'TemplateWidgetController@showSelectValueByParentId')->name('goods.template.widget.show-select-value')->middleware('permission:goods.template.widget.show-select-value');
                // 保存修改
                Route::post('edit', 'TemplateWidgetController@edit')->name('goods.template.widget.edit')->middleware('permission:goods.template.widget.edit');
                // 获取模版所有组件
                Route::get('all/{templateId}', 'TemplateWidgetController@showAll')->name('goods.template.widget.show.all')->middleware('permission:goods.template.widget.show.all');
                // 保存
                Route::post('/', 'TemplateWidgetController@store')->name('goods.template.widget.store')->middleware('permission:goods.template.widget.store');
                // 删除
                Route::post('destroy', 'TemplateWidgetController@destroy')->name('goods.template.widget.destroy')->middleware('permission:goods.template.widget.destroy');
            });
        });
    });

    // 用户
    Route::namespace('User')->prefix('user')->group(function (){
        // 用户账号列表
        Route::namespace('Frontend')->prefix('frontend')->group(function () {
            Route::get('/', 'UserController@index')->name('frontend.user.index')->middleware('permission:frontend.user.index');
            // 更新用户资料
            Route::post('edit', 'UserController@edit')->name('frontend.user.edit')->middleware('permission:frontend.user.edit');
            // 手动加款
            Route::post('recharge', 'UserController@recharge')->name('frontend.user.recharge')->middleware('permission:frontend.user.recharge');
            // 用户资料
            Route::get('show/{userId}', 'UserController@show')->name('frontend.user.show')->middleware('permission:frontend.user.show');
            // 实名认证
            Route::get('authentication/{userId}', 'UserController@authentication')->name('frontend.user.authentication')->middleware('permission:frontend.user.authentication');
            // 权重
            Route::prefix('weight')->group(function () {
                // 用户权重列表
                Route::get('/', 'WeightController@index')->name('frontend.user.weight.index')->middleware('permission:frontend.user.weight.index');
                // 查看
                Route::get('/{id}', 'WeightController@show')->name('frontend.user.weight.show')->middleware('permission:frontend.user.weight.show');
                // 修改用户权重
                Route::post('edit', 'WeightController@edit')->name('frontend.user.weight.edit')->middleware('permission:frontend.user.weight.edit');
            });
        });
        // 后台账号列表
        Route::namespace('Frontend')->prefix('backend')->group(function () {
            Route::get('/', 'UserController@index')->name('backend.user.index')->middleware('permission:backend.user.index');
        });
    });


    Route::namespace('Rbac')->prefix('rbac')->group(function () {
        // 前台角色
        // Route::resource('roles', 'RoleController', ['except' => ['show']]);
        Route::get('roles', 'RoleController@index')->name('roles.index')->middleware('permission:roles.index');
        Route::get('roles/create', 'RoleController@create')->name('roles.create')->middleware('permission:roles.create');
        Route::post('roles', 'RoleController@store')->name('roles.store')->middleware('permission:roles.store');
        Route::get('roles/{id}/edit', 'RoleController@edit')->name('roles.edit')->middleware('permission:roles.edit');
        Route::put('roles/{id}', 'RoleController@update')->name('roles.update')->middleware('permission:roles.update');
        Route::delete('roles/{id}', 'RoleController@destroy')->name('roles.destroy')->middleware('permission:roles.destroy');
        // 后台角色
        // Route::resource('admin-roles', 'AdminRoleController', ['except' => ['show']]);
        Route::get('admin-roles', 'AdminRoleController@index')->name('admin-roles.index')->middleware('permission:admin-roles.index');
        Route::get('admin-roles/create', 'AdminRoleController@create')->name('admin-roles.create')->middleware('permission:admin-roles.create');
        Route::post('admin-roles', 'AdminRoleController@store')->name('admin-roles.store')->middleware('permission:admin-roles.store');
        Route::get('admin-roles/{id}/edit', 'AdminRoleController@edit')->name('admin-roles.edit')->middleware('permission:admin-roles.edit');
        Route::put('admin-roles/{id}', 'AdminRoleController@update')->name('admin-roles.update')->middleware('permission:admin-roles.update');
        Route::delete('admin-roles/{id}', 'AdminRoleController@destroy')->name('admin-roles.destroy')->middleware('permission:admin-roles.destroy');
        // 前台权限
        // Route::resource('permissions', 'PermissionController', ['except' => ['show']]);
        Route::get('permissions', 'PermissionController@index')->name('permissions.index')->middleware('permission:permissions.index');
        Route::get('permissions/create', 'PermissionController@create')->name('permissions.create')->middleware('permission:permissions.create');
        Route::post('permissions', 'PermissionController@store')->name('permissions.store')->middleware('permission:permissions.store');
        Route::get('permissions/{id}/edit', 'PermissionController@edit')->name('permissions.edit')->middleware('permission:permissions.edit');
        Route::put('permissions/{id}', 'PermissionController@update')->name('permissions.update')->middleware('permission:permissions.update');
        Route::delete('permissions/{id}', 'PermissionController@destroy')->name('permissions.destroy')->middleware('permission:permissions.destroy');
        // 后台权限
        // Route::resource('admin-permissions', 'AdminPermissionController', ['except' => ['show']]);
        Route::get('admin-permissions', 'AdminPermissionController@index')->name('admin-permissions.index')->middleware('permission:admin-permissions.index');
        Route::get('admin-permissions/create', 'AdminPermissionController@create')->name('admin-permissions.create')->middleware('permission:admin-permissions.create');
        Route::post('admin-permissions', 'AdminPermissionController@store')->name('admin-permissions.store')->middleware('permission:admin-permissions.store');
        Route::get('admin-permissions/{id}/edit', 'AdminPermissionController@edit')->name('admin-permissions.edit')->middleware('permission:admin-permissions.edit');
        Route::put('admin-permissions/{id}', 'AdminPermissionController@update')->name('admin-permissions.update')->middleware('permission:admin-permissions.update');
        Route::delete('admin-permissions/{id}', 'AdminPermissionController@destroy')->name('admin-permissions.destroy')->middleware('permission:admin-permissions.destroy');
        // 前台账号分配角色
        // Route::resource('groups', 'GroupController');
        Route::get('groups', 'GroupController@index')->name('groups.index')->middleware('permission:groups.index');
        Route::get('groups/create', 'GroupController@create')->name('groups.create')->middleware('permission:groups.create');
        Route::post('groups', 'GroupController@store')->name('groups.store')->middleware('permission:groups.store');
        Route::get('groups/{id}/edit', 'GroupController@edit')->name('groups.edit')->middleware('permission:groups.edit');
        Route::get('groups/{id}', 'GroupController@show')->name('groups.show')->middleware('permission:groups.show');
        Route::put('groups/{id}', 'GroupController@update')->name('groups.update')->middleware('permission:groups.update');
        Route::delete('groups/{id}', 'GroupController@destroy')->name('groups.destroy')->middleware('permission:groups.destroy');
        // 后台账号分配角色
        // Route::resource('admin-groups', 'AdminGroupController');
        Route::get('admin-groups', 'AdminGroupController@index')->name('admin-groups.index')->middleware('permission:admin-groups.index');
        Route::get('admin-groups/create', 'AdminGroupController@create')->name('admin-groups.create')->middleware('permission:admin-groups.create');
        Route::post('admin-groups', 'AdminGroupController@store')->name('admin-groups.store')->middleware('permission:admin-groups.store');
        Route::get('admin-groups/{id}/edit', 'AdminGroupController@edit')->name('admin-groups.edit')->middleware('permission:admin-groups.edit');
        Route::get('admin-groups/{id}', 'AdminGroupController@show')->name('admin-groups.show')->middleware('permission:admin-groups.show');
        Route::put('admin-groups/{id}', 'AdminGroupController@update')->name('admin-groups.update')->middleware('permission:admin-groups.update');
        Route::delete('admin-groups/{id}', 'AdminGroupController@destroy')->name('admin-groups.destroy')->middleware('permission:admin-groups.destroy');
        // 前台模块
        // Route::resource('modules', 'ModuleController', ['except' => ['show']]);
        Route::get('modules', 'ModuleController@index')->name('modules.index')->middleware('permission:modules.index');
        Route::get('modules/create', 'ModuleController@create')->name('modules.create')->middleware('permission:modules.create');
        Route::post('modules', 'ModuleController@store')->name('modules.store')->middleware('permission:modules.store');
        Route::get('modules/{id}/edit', 'ModuleController@edit')->name('modules.edit')->middleware('permission:modules.edit');
        Route::put('modules/{id}', 'ModuleController@update')->name('modules.update')->middleware('permission:modules.update');
        Route::delete('modules/{id}', 'ModuleController@destroy')->name('modules.destroy')->middleware('permission:modules.destroy');
        // 后台模块
        // Route::resource('admin-modules', 'AdminModuleController', ['except' => ['show']]);
        Route::get('admin-modules', 'AdminModuleController@index')->name('admin-modules.index')->middleware('permission:admin-modules.index');
        Route::get('admin-modules/create', 'AdminModuleController@create')->name('admin-modules.create')->middleware('permission:admin-modules.create');
        Route::post('admin-modules', 'AdminModuleController@store')->name('admin-modules.store')->middleware('permission:admin-modules.store');
        Route::get('admin-modules/{id}/edit', 'AdminModuleController@edit')->name('admin-modules.edit')->middleware('permission:admin-modules.edit');
        Route::put('admin-modules/{id}', 'AdminModuleController@update')->name('admin-modules.update')->middleware('permission:admin-modules.update');
        Route::delete('admin-modules/{id}', 'AdminModuleController@destroy')->name('admin-modules.destroy')->middleware('permission:admin-modules.destroy');
        // 后台账号
        Route::get('admin-accounts', 'AdminAccountController@index')->name('admin-accounts.index')->middleware('permission:admin-accounts.index');
        Route::get('admin-accounts/{id}/edit', 'AdminAccountController@edit')->name('admin-accounts.edit')->middleware('permission:admin-accounts.edit');
        Route::put('admin-accounts/{id}', 'AdminAccountController@update')->name('admin-accounts.update')->middleware('permission:admin-accounts.update');
    });

    Route::namespace('Account')->prefix('account')->group(function () {
        // 账号管理-我的账号
        Route::get('login-history', 'LoginRecordController@index')->name('login-record.index');
        // 实名认证 - 通过
        Route::post('pass', 'PassOrRefuseController@pass')->name('pass-or-refuse.pass')->middleware('permission:pass-or-refuse.pass');
        // 实名认证 - 拒绝
        Route::post('refuse', 'PassOrRefuseController@refuse')->name('pass-or-refuse.refuse')->middleware('permission:pass-or-refuse.refuse');
        // 实名认证列表
        Route::get('admin-idents', 'AdminIdentController@index')->name('admin-idents.index')->middleware('permission:admin-idents.index');
        // 实名认证详情
        Route::get('admin-idents/{id}', 'AdminIdentController@show')->name('admin-idents.show')->middleware('permission:admin-idents.show');

    });

    // 订单
    Route::namespace('Order')->prefix('order')->group(function () {
        // 平台订单
        Route::prefix('platform')->group(function () {
            // 订单列表
            Route::get('/', 'PlatformController@index')->name('order.platform.index')->middleware('permission:order.platform.index');
            // 订单内容
            Route::get('content/{id}', 'PlatformController@content')->name('order.platform.content')->middleware('permission:order.platform.content');
            // 订单操作记录
            Route::get('record/{id}', 'PlatformController@record')->name('order.platform.record')->middleware('permission:order.platform.record');
            // 申请退款
            Route::get('refund-application', 'PlatformController@refundApplication')->name('order.platform.refund-application')->middleware('permission:order.platform.refund-application');
            // 修改状态
            Route::post('change-status', 'PlatformController@changeStatus')->name('order.platform.change-status')->middleware('permission:order.platform.change-status');
        });
        // 外部订单
        Route::prefix('foreign')->group( function () {
            // 订单列表
            Route::get('/', 'ForeignController@index')->name('order.foreign.index')->middleware('permission:order.platform.index');
        });
    });

    // 财务
    Route::namespace('Finance')->prefix('finance')->group(function () {
        // 当前资产
        Route::get('platform-asset', 'PlatformAssetController@index')->name('finance.platform-asset')->middleware('permission:finance.platform-asset');
        // 资金流水
        Route::get('platform-amount-flow', 'PlatformAmountFlowController@index')->name('finance.platform-amount-flow')->middleware('permission:finance.platform-amount-flow');
        // 资金流水导出
        Route::get('platform-amount-flow/export', 'PlatformAmountFlowController@export')->name('finance.platform-amount-flow.export')->middleware('permission:finance.platform-amount-flow.export');
        // 资产日报
        Route::get('platform-asset-daily', 'PlatformAssetDailyController@index')->name('finance.platform-asset-daily')->middleware('permission:finance.platform-asset-daily');
        // 资产日报导出
        Route::get('platform-asset-daily/export', 'PlatformAssetDailyController@export')->name('finance.platform-asset-daily.export')->middleware('permission:finance.platform-asset-daily.export');
        // 用户资产
        Route::get('user-asset', 'UserAssetController@index')->name('finance.user-asset')->middleware('permission:finance.user-asset');
        // 用户资产日报
        Route::get('user-asset-daily', 'UserAssetDailyController@index')->name('finance.user-asset-daily')->middleware('permission:finance.user-asset-daily');
        // 用户资产流水
        Route::get('user-amount-flow', 'UserAmountFlowController@index')->name('finance.user-amount-flow')->middleware('permission:finance.user-amount-flow');
        // 用户提现列表
        Route::get('user-widthdraw-order', 'UserWithdrawOrderController@index')->name('finance.user-widthdraw-order')->middleware('permission:finance.user-widthdraw-order');
        // 用户提现通过
        Route::post('user-widthdraw-order/complete/{userWithdrawOrder}', 'UserWithdrawOrderController@complete')->name('finance.user-widthdraw-order.complete')->middleware('permission:finance.user-widthdraw-order.complete');
        // 用户提现拒绝
        Route::post('user-widthdraw-order/refuse/{userWithdrawOrder}', 'UserWithdrawOrderController@refuse')->name('finance.user-widthdraw-order.refuse')->middleware('permission:finance.user-widthdraw-order.refuse');
    });

    Route::prefix('template')->group(function () {
        Route::get('form', 'TemplateController@form')->name('template.form');
        Route::get('icons1', 'TemplateController@icons1')->name('template.icons1');
        Route::get('icons2', 'TemplateController@icons2')->name('template.icons2');
    });

    // 违规管理
    Route::namespace('Punish')->prefix('punish')->group(function () {
        // 违规列表和违规增删改查
        Route::get('punishes', 'PunishController@index')->name('punishes.index')->middleware('permission:punishes.index');
        Route::get('punishes/create', 'PunishController@create')->name('punishes.create')->middleware('permission:punishes.create');
        Route::post('punishes', 'PunishController@store')->name('punishes.store')->middleware('permission:punishes.store');
        Route::get('punishes/{id}', 'PunishController@show')->name('punishes.show')->middleware('permission:punishes.show');
        Route::get('punishes/{id}/edit', 'PunishController@edit')->name('punishes.edit')->middleware('permission:punishes.edit');
        Route::put('punishes/{id}', 'PunishController@update')->name('punishes.update')->middleware('permission:punishes.update');
        Route::delete('punishes/{id}', 'PunishController@destroy')->name('punishes.destroy')->middleware('permission:punishes.destroy');
        Route::post('punishes/user', 'PunishController@orders')->name('punishes.user')->middleware('permission:punishes.user');
        // 图片上传地址
        Route::post('punishes/upload-images', 'PunishController@uploadImages')->name('punishes.upload-images')->middleware('permission:punishes.upload-images');
        // 奖惩管理
        Route::post('execute/sub-money', 'ExecuteController@subMoney')->name('execute.sub-money')->middleware('permission:execute.sub-money');
        Route::post('execute/add-money', 'ExecuteController@addMoney')->name('execute.add-money')->middleware('permission:execute.add-money');
        Route::post('execute/add-weight', 'ExecuteController@addWeight')->name('execute.add-weight')->middleware('permission:execute.add-weight');
        Route::post('execute/sub-weight', 'ExecuteController@subWeight')->name('execute.sub-weight')->middleware('permission:execute.sub-weight');
        Route::post('execute/forbidden', 'ExecuteController@forbidden')->name('execute.forbidden')->middleware('permission:execute.forbidden');
        // 后台详情里面的操作
        Route::post('execute/pass', 'ExecuteController@pass')->name('execute.pass')->middleware('permission:execute.pass');
        Route::post('execute/refuse', 'ExecuteController@refuse')->name('execute.refuse')->middleware('permission:execute.refuse');  
    });
});
