<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\MapController;

Route::get('/map', [MapController::class, 'regions']);

