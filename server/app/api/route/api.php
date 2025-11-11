<?php
use think\facade\Route;

Route::get('/', 'Index/index');
Route::get('/cros', 'Index/index')->middleware(\app\api\middleware\Auth::class);