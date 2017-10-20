<?php

Route::namespace('Backend\Auth')->group(function () {
    // 登录
    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login');
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
    Route::resource('system-logs', 'SystemLogController', ['only' => ['index']]);
    // 账号管理-我的账号
    Route::get('login-history', 'LoginRecordController@index')->name('login-record.index');

    Route::namespace('Goods')->prefix('goods')->group(function (){

        Route::prefix('template')->group(function (){
            // 列表
            Route::get('/', 'TemplateController@index')->name('goods.template.index');
            // 创建视图
            Route::get('create', 'TemplateController@index')->name('goods.template.created');
            // 保存
            Route::post('/', 'TemplateController@store')->name('goods.template.store');
            // 查看
            Route::get('/{templateId}', 'TemplateController@show')->name('goods.template.show');
            // 删除
            Route::post('destroy{templateId}', 'TemplateController@destroy')->name('goods.template.destroy');

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

    Route::namespace('Rbac')->prefix('rbac')->group(function () {
        // 前台角色
        Route::resource('roles', 'RoleController', ['except' => ['show']]);
        // 后台角色
        Route::resource('admin-roles', 'AdminRoleController', ['except' => ['show']]);
        // 前台权限
        Route::resource('permissions', 'PermissionController', ['except' => ['show']]);
        // 后台权限
        Route::resource('admin-permissions', 'AdminPermissionController', ['except' => ['show']]);
        // 前台账号分配角色
        Route::resource('groups', 'GroupController');
        // 后台账号分配角色
        Route::resource('admin-groups', 'AdminGroupController');
        // 前台模块
        Route::resource('modules', 'ModuleController', ['except' => ['show']]);
        // 后台模块
        Route::resource('admin-modules', 'AdminModuleController', ['except' => ['show']]);
        // 前台账号
        Route::get('accounts', 'AccountController@index')->name('accounts.index');
        // 后台账号
        Route::get('admin-accounts', 'AdminAccountController@index')->name('admin-accounts.index');
    });

    Route::prefix('order')->group(function (){
        Route::get('/', 'OrderController@index')->name('order.index');
    });

    Route::namespace('Finance')->prefix('finance')->group(function () {
        Route::get('platform-asset', 'PlatformAssetController@index')->name('finance.platform-asset');

        Route::get('platform-amount-flow', 'PlatformAmountFlowController@index')->name('finance.platform-amount-flow');
        Route::get('platform-amount-flow/export', 'PlatformAmountFlowController@export')->name('finance.platform-amount-flow.export');

        Route::get('platform-asset-daily', 'PlatformAssetDailyController@index')->name('finance.platform-asset-daily');
        Route::get('platform-asset-daily/export', 'PlatformAssetDailyController@export')->name('finance.platform-asset-daily.export');

        Route::get('user-asset', 'UserAssetController@index')->name('finance.user-asset');

        Route::get('user-amount-flow', 'UserAmountFlowController@index')->name('finance.user-amount-flow');
    });

    Route::prefix('template')->group(function (){
        Route::get('form', 'TemplateController@form')->name('template.form');
        Route::get('icons1', 'TemplateController@icons1')->name('template.icons1');
        Route::get('icons2', 'TemplateController@icons2')->name('template.icons2');
    });
});