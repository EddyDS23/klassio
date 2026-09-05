<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['throttle:auth'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth','active','role:admin'])->group(function(){
    Route::get('/admin',fn()=>'Admin Dashboard');
});

Route::middleware(['auth','active','role:teacher'])->group(function(){
    Route::get('/teacher/dashboard', fn()=>'Teacher Dashboard');
});

Route::middleware(['auth','active','role:student'])->group(function(){
    Route::get('/student/dashboard',fn()=>'Student Dashboard');
});
