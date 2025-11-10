<?php
use think\facade\Route;


// 管理员路由组
Route::group('admin', function () {
    // 用户登录
    Route::post('auth/login', 'admin/Auth/login');
    // 用户退出登录
    Route::get('auth/logout', 'admin/Auth/logout');
    // 刷新token
    Route::post('auth/refresh', 'admin/Auth/refreshToken');
    // 获取当前用户信息
    Route::get('auth/user', 'admin/Auth/user');
});
