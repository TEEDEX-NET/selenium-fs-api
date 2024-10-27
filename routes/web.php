<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ApiController::class, 'index'])->name('index');
Route::post('/upload-image', [ApiController::class, 'uploadImage']);
