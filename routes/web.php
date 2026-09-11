<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CrosswordController;
use App\Http\Controllers\KahootController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\WordsearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('login'));

Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['throttle:auth'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
});

Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'active', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{id}', [ClassController::class, 'show'])->name('classes.show');
    Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::get('/classes/{id}/students', [ClassController::class, 'students'])->name('classes.students');
    Route::delete('/classes/{id}/students/{studentId}', [ClassController::class, 'removeStudent'])->name('classes.students.remove');
    Route::patch('/classes/{id}/regenerate-code', [ClassController::class, 'regenerateCode'])->name('classes.regenerate-code');
    Route::patch('/classes/{id}/archive', [ClassController::class, 'archive'])->name('classes.archive');
    Route::patch('/classes/{id}/unarchive', [ClassController::class, 'unarchive'])->name('classes.unarchive');
    Route::get('/classes/{id}/activities', [ActivityController::class, 'teacherIndex'])->name('activities.index');
    Route::get('/classes/{id}/activities/create', [ActivityController::class, 'teacherCreate'])->name('activities.create');
    Route::post('/classes/{id}/activities', [ActivityController::class, 'teacherStore'])->name('activities.store');
    Route::get('/activities/{id}', [ActivityController::class, 'teacherShow'])->name('activities.show');
    Route::get('/activities/{id}/edit', [ActivityController::class, 'teacherEdit'])->name('activities.edit');
    Route::put('/activities/{id}', [ActivityController::class, 'teacherUpdate'])->name('activities.update');
    Route::post('/activities/{id}/publish', [ActivityController::class, 'publish'])->name('activities.publish');
    Route::post('/activities/{id}/close', [ActivityController::class, 'close'])->name('activities.close');
    Route::get('/activities/{id}/crossword/configure', [CrosswordController::class, 'configure'])->name('crossword.configure');
    Route::post('/activities/{id}/crossword', [CrosswordController::class, 'store'])->name('crossword.store');
    Route::get('/activities/{id}/crossword/edit', [CrosswordController::class, 'edit'])->name('crossword.edit');
    Route::put('/activities/{id}/crossword', [CrosswordController::class, 'update'])->name('crossword.update');
    Route::get('/activities/{id}/kahoot/configure', [KahootController::class, 'configure'])->name('kahoot.configure');
    Route::post('/activities/{id}/kahoot', [KahootController::class, 'store'])->name('kahoot.store');
    Route::get('/activities/{id}/kahoot/edit', [KahootController::class, 'edit'])->name('kahoot.edit');
    Route::put('/activities/{id}/kahoot', [KahootController::class, 'update'])->name('kahoot.update');
    Route::get('/activities/{id}/matching/configure', [MatchingController::class, 'configure'])->name('matching.configure');
    Route::post('/activities/{id}/matching', [MatchingController::class, 'store'])->name('matching.store');
    Route::get('/activities/{id}/matching/edit', [MatchingController::class, 'edit'])->name('matching.edit');
    Route::put('/activities/{id}/matching', [MatchingController::class, 'update'])->name('matching.update');
    Route::get('/activities/{id}/wordsearch/configure', [WordsearchController::class, 'configure'])->name('wordsearch.configure');
    Route::post('/activities/{id}/wordsearch', [WordsearchController::class, 'store'])->name('wordsearch.store');
    Route::get('/activities/{id}/wordsearch/edit', [WordsearchController::class, 'edit'])->name('wordsearch.edit');
    Route::put('/activities/{id}/wordsearch', [WordsearchController::class, 'update'])->name('wordsearch.update');
    
});

Route::prefix('student')->name('student.')->middleware(['auth', 'active', 'role:student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'studentIndex'])->name('classes.index');
    Route::post('/classes/join', [ClassController::class, 'join'])->name('class.join');
    Route::get('/classes/{id}/students', [ClassController::class, 'studentStudents'])->name('class.students');
    Route::get('/classes/{id}', [ClassController::class, 'studentShow'])->name('class.show');
    Route::get('/classes/{id}/activities', [ActivityController::class, 'studentIndex'])->name('activities.index');
    Route::get('/activities/{id}', [ActivityController::class, 'studentShow'])->name('activities.show');
    Route::get('/activities/{id}/crossword/play', [CrosswordController::class, 'play'])->name('crossword.play');
    Route::post('/crossword/answer', [CrosswordController::class, 'answer'])->name('crossword.answer');
    Route::get('/activities/{id}/kahoot/play', [KahootController::class, 'play'])->name('kahoot.play');
    Route::post('/kahoot/answer', [KahootController::class, 'answer'])->name('kahoot.answer');
    Route::get('/activities/{id}/matching/play', [MatchingController::class, 'play'])->name('matching.play');
    Route::post('/matching/answer', [MatchingController::class, 'answer'])->name('matching.answer');
    Route::get('/activities/{id}/wordsearch/play', [WordsearchController::class, 'play'])->name('wordsearch.play');
    Route::post('/wordsearch/answer', [WordsearchController::class, 'answer'])->name('wordsearch.answer');
    Route::post('/activities/{id}/start', [ParticipationController::class, 'start'])->name('participation.start');
    Route::post('/activities/{id}/finish', [ParticipationController::class, 'finish'])->name('participation.finish');
    Route::post('/activities/{id}/abandon', [ParticipationController::class, 'abandon'])->name('participation.abandon');
    Route::get('/activities/{id}/result', [ParticipationController::class, 'result'])->name('participation.result');
    Route::post('/activities/{id}/expire',[ParticipationController::class, 'expire'])->name('participation.expire');
});
