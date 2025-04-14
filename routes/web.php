<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-redis', function () {
    Cache::put('courier_redis_test', 'Redis is connected ✅', 60);
    return Cache::get('courier_redis_test');
});
