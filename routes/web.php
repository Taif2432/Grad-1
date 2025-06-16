<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-proc', function () {
    return function_exists('proc_open') ? 'proc_open is enabled' : 'proc_open is disabled';
});

