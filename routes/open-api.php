<?php
Route::prefix('auth')->group(function($router) {
    $router->post('login', 'Auth\AuthController@login');
    $router->post('logout', 'OpenApi\Auth\AuthController@logout');
});

Route::middleware('open.api')->prefix('game')->namespace('Game')->group(function($router) {
    $router->get('/','IndexController@index');
});


Route::middleware('open.api')->group(function($router) {
//    $router->get('profile','UserController@profile');
});