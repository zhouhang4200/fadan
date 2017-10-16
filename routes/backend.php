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
        Route::resource('roles', 'RoleController', ['except' => ['show']]);
        Route::resource('admin-roles', 'AdminRoleController', ['except' => ['show']]);
        Route::resource('permissions', 'PermissionController', ['except' => ['show']]);
        Route::resource('admin-permissions', 'AdminPermissionController', ['except' => ['show']]);
        Route::resource('groups', 'GroupController',['except' => ['show']]);
        Route::resource('admin-groups', 'AdminGroupController',['except' => ['show']]);
        Route::resource('modules', 'ModuleController', ['except' => ['show']]);
        Route::resource('admin-modules', 'AdminModuleController', ['except' => ['show']]);
    });

    Route::namespace('Account')->prefix('account')->group(function () {
        Route::get('accounts', 'AccountController@index')->name('accounts.index');
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

        Route::get('user/asset', 'UserController@asset')->name('finance.user.asset');
        Route::get('user/flow', 'UserController@flow')->name('finance.user.flow');
    });
});