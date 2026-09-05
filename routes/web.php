<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
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

Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class,'admin'])->name('admin.dashboard');
});

Route::middleware(['auth', 'active', 'role:teacher'])->group(function () {
    Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('teacher.classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('teacher.classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('teacher.classes.store');
    Route::get('/classes/{id}', [ClassController::class, 'show'])->name('teacher.classes.show');
    Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->name('teacher.classes.edit');
    Route::put('/classes/{id}', [ClassController::class, 'update'])->name('teacher.classes.update');
    Route::get('/classes/{id}/students', [ClassController::class, 'students'])->name('teacher.classes.students');
    Route::delete('/classes/{id}/students/{studentId}', [ClassController::class, 'removeStudent'])->name('teacher.classes.students.remove');
    Route::patch('/classes/{id}/regenerate-code', [ClassController::class, 'regenerateCode'])->name('teacher.classes.regenerate-code');
    Route::patch('/classes/{id}/archive', [ClassController::class, 'archive'])->name('teacher.classes.archive');
    Route::patch('/classes/{id}/unarchive', [ClassController::class, 'unarchive'])->name('teacher.classes.unarchive');
});

Route::middleware(['auth', 'active', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [DashboardController::class,'student'])->name('student.dashboard');
    Route::get('/student/classes', [ClassController::class, 'studentIndex'])->name('student.classes.index');
    Route::post('/student/classes/join', [ClassController::class, 'join'])->name('student.class.join');
    Route::get('/student/classes/{id}/students', [ClassController::class, 'studentStudents'])->name('student.class.students');
    Route::get('/student/classes/{id}', [ClassController::class, 'studentShow'])->name('student.class.show');
    
});
