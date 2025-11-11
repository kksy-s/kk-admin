<?php
use think\facade\Route;

Route::post('/auth/login', 'Auth/login');
Route::get('', 'Index/index');

Route::group('/', function () {
    Route::get('auth/user', 'Auth/user');
    
})->middleware(\app\adminapi\middleware\Auth::class);
