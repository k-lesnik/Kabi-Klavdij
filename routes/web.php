<?php

use App\Http\Controllers\FursController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FursController::class, 'index'])->name('furs.index');
