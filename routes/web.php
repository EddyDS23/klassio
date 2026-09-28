<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminResultController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CrosswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KahootController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RouletteController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WordsearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['throttle:auth'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->where(['id' => '[1-9][0-9]*'])->name('users.show');
    Route::patch('/users/{id}/status', [AdminUserController::class, 'updateStatus'])->where(['id' => '[1-9][0-9]*'])->name('users.update-status');

    Route::get('/classes', [AdminClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/{id}', [AdminClassController::class, 'show'])->where(['id' => '[1-9][0-9]*'])->name('classes.show');

    Route::get('/activities', [AdminActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/{id}', [AdminActivityController::class, 'show'])->where(['id' => '[1-9][0-9]*'])->name('activities.show');

    Route::get('/results', [AdminResultController::class, 'index'])->name('results.index');
    Route::get('/results/{id}', [AdminResultController::class, 'show'])->where(['id' => '[1-9][0-9]*'])->name('results.show');
});

Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'active', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{id}', [ClassController::class, 'show'])->where(['id' => '[1-9][0-9]*'])->name('classes.show');
    Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->where(['id' => '[1-9][0-9]*'])->name('classes.edit');
    Route::put('/classes/{id}', [ClassController::class, 'update'])->where(['id' => '[1-9][0-9]*'])->name('classes.update');
    Route::get('/classes/{id}/students', [ClassController::class, 'students'])->where(['id' => '[1-9][0-9]*'])->name('classes.students');
    Route::delete('/classes/{id}/students/{studentId}', [ClassController::class, 'removeStudent'])->where(['id' => '[1-9][0-9]*', 'studentId' => '[1-9][0-9]*'])->name('classes.students.remove');
    Route::patch('/classes/{id}/regenerate-code', [ClassController::class, 'regenerateCode'])->where(['id' => '[1-9][0-9]*'])->name('classes.regenerate-code');
    Route::patch('/classes/{id}/archive', [ClassController::class, 'archive'])->where(['id' => '[1-9][0-9]*'])->name('classes.archive');
    Route::patch('/classes/{id}/unarchive', [ClassController::class, 'unarchive'])->where(['id' => '[1-9][0-9]*'])->name('classes.unarchive');
    Route::get('/classes/{id}/activities', [ActivityController::class, 'teacherIndex'])->where(['id' => '[1-9][0-9]*'])->name('activities.index');
    Route::get('/classes/{id}/activities/create', [ActivityController::class, 'teacherCreate'])->where(['id' => '[1-9][0-9]*'])->name('activities.create');
    Route::post('/classes/{id}/activities', [ActivityController::class, 'teacherStore'])->where(['id' => '[1-9][0-9]*'])->name('activities.store');
    Route::get('/activities/{id}', [ActivityController::class, 'teacherShow'])->where(['id' => '[1-9][0-9]*'])->name('activities.show');
    Route::get('/activities/{id}/results', [ParticipationController::class, 'teacherResults'])->where(['id' => '[1-9][0-9]*'])->name('activities.results');
    Route::get('/activities/{id}/ranking', [RankingController::class, 'ranking'])->where(['id' => '[1-9][0-9]*'])->name('activities.ranking');
    Route::get('/activities/{id}/report', [RankingController::class, 'report'])->where(['id' => '[1-9][0-9]*'])->name('activities.report');
    Route::get('/activities/{id}/edit', [ActivityController::class, 'teacherEdit'])->where(['id' => '[1-9][0-9]*'])->name('activities.edit');
    Route::put('/activities/{id}', [ActivityController::class, 'teacherUpdate'])->where(['id' => '[1-9][0-9]*'])->name('activities.update');
    Route::post('/activities/{id}/publish', [ActivityController::class, 'publish'])->where(['id' => '[1-9][0-9]*'])->name('activities.publish');
    Route::post('/activities/{id}/close', [ActivityController::class, 'close'])->where(['id' => '[1-9][0-9]*'])->name('activities.close');
    Route::get('/activities/{id}/crossword/configure', [CrosswordController::class, 'configure'])->where(['id' => '[1-9][0-9]*'])->name('crossword.configure');
    Route::post('/activities/{id}/crossword', [CrosswordController::class, 'store'])->where(['id' => '[1-9][0-9]*'])->name('crossword.store');
    Route::get('/activities/{id}/crossword/edit', [CrosswordController::class, 'edit'])->where(['id' => '[1-9][0-9]*'])->name('crossword.edit');
    Route::put('/activities/{id}/crossword', [CrosswordController::class, 'update'])->where(['id' => '[1-9][0-9]*'])->name('crossword.update');
    Route::get('/activities/{id}/kahoot/configure', [KahootController::class, 'configure'])->where(['id' => '[1-9][0-9]*'])->name('kahoot.configure');
    Route::post('/activities/{id}/kahoot', [KahootController::class, 'store'])->where(['id' => '[1-9][0-9]*'])->name('kahoot.store');
    Route::get('/activities/{id}/kahoot/edit', [KahootController::class, 'edit'])->where(['id' => '[1-9][0-9]*'])->name('kahoot.edit');
    Route::put('/activities/{id}/kahoot', [KahootController::class, 'update'])->where(['id' => '[1-9][0-9]*'])->name('kahoot.update');
    Route::get('/activities/{id}/matching/configure', [MatchingController::class, 'configure'])->where(['id' => '[1-9][0-9]*'])->name('matching.configure');
    Route::post('/activities/{id}/matching', [MatchingController::class, 'store'])->where(['id' => '[1-9][0-9]*'])->name('matching.store');
    Route::get('/activities/{id}/matching/edit', [MatchingController::class, 'edit'])->where(['id' => '[1-9][0-9]*'])->name('matching.edit');
    Route::put('/activities/{id}/matching', [MatchingController::class, 'update'])->where(['id' => '[1-9][0-9]*'])->name('matching.update');
    Route::get('/activities/{id}/roulette/configure', [RouletteController::class, 'configure'])->where(['id' => '[1-9][0-9]*'])->name('roulette.configure');
    Route::post('/activities/{id}/roulette', [RouletteController::class, 'store'])->where(['id' => '[1-9][0-9]*'])->name('roulette.store');
    Route::get('/activities/{id}/roulette/edit', [RouletteController::class, 'edit'])->where(['id' => '[1-9][0-9]*'])->name('roulette.edit');
    Route::put('/activities/{id}/roulette', [RouletteController::class, 'update'])->where(['id' => '[1-9][0-9]*'])->name('roulette.update');
    Route::get('/activities/{id}/wordsearch/configure', [WordsearchController::class, 'configure'])->where(['id' => '[1-9][0-9]*'])->name('wordsearch.configure');
    Route::post('/activities/{id}/wordsearch', [WordsearchController::class, 'store'])->where(['id' => '[1-9][0-9]*'])->name('wordsearch.store');
    Route::get('/activities/{id}/wordsearch/edit', [WordsearchController::class, 'edit'])->where(['id' => '[1-9][0-9]*'])->name('wordsearch.edit');
    Route::put('/activities/{id}/wordsearch', [WordsearchController::class, 'update'])->where(['id' => '[1-9][0-9]*'])->name('wordsearch.update');
    Route::get('/activities/{id}/teams', [TeamController::class, 'index'])->where(['id' => '[1-9][0-9]*'])->name('teams.index');
    Route::post('/activities/{id}/teams', [TeamController::class, 'store'])->where(['id' => '[1-9][0-9]*'])->name('teams.store');
    Route::post('/activities/{id}/teams/{teamId}/members', [TeamController::class, 'addMember'])->where(['id' => '[1-9][0-9]*', 'teamId' => '[1-9][0-9]*'])->name('teams.members.add');
    Route::delete('/activities/{id}/teams/{teamId}/members/{studentId}', [TeamController::class, 'removeMember'])->where(['id' => '[1-9][0-9]*', 'teamId' => '[1-9][0-9]*', 'studentId' => '[1-9][0-9]*'])->name('teams.members.remove');
    Route::delete('/activities/{id}/teams/{teamId}', [TeamController::class, 'destroy'])->where(['id' => '[1-9][0-9]*', 'teamId' => '[1-9][0-9]*'])->name('teams.destroy');
    Route::post('/activities/{id}/teams/randomize', [TeamController::class, 'randomize'])->where(['id' => '[1-9][0-9]*'])->name('teams.randomize');
    Route::post('/activities/{id}/random-student', [TeamController::class, 'randomStudent'])->where(['id' => '[1-9][0-9]*'])->name('teams.random-student');
    Route::post('/activities/{id}/random-team', [TeamController::class, 'randomTeam'])->where(['id' => '[1-9][0-9]*'])->name('teams.random-team');
});

Route::prefix('student')->name('student.')->middleware(['auth', 'active', 'role:student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'studentIndex'])->name('classes.index');
    Route::post('/classes/join', [ClassController::class, 'join'])->name('class.join');
    Route::get('/classes/{id}/students', [ClassController::class, 'studentStudents'])->where(['id' => '[1-9][0-9]*'])->name('class.students');
    Route::get('/classes/{id}', [ClassController::class, 'studentShow'])->where(['id' => '[1-9][0-9]*'])->name('class.show');
    Route::get('/classes/{id}/activities', [ActivityController::class, 'studentIndex'])->where(['id' => '[1-9][0-9]*'])->name('activities.index');
    Route::get('/activities/{id}', [ActivityController::class, 'studentShow'])->where(['id' => '[1-9][0-9]*'])->name('activities.show');
    Route::get('/activities/{id}/crossword/play', [CrosswordController::class, 'play'])->where(['id' => '[1-9][0-9]*'])->name('crossword.play');
    Route::post('/crossword/answer', [CrosswordController::class, 'answer'])->name('crossword.answer');
    Route::get('/activities/{id}/kahoot/play', [KahootController::class, 'play'])->where(['id' => '[1-9][0-9]*'])->name('kahoot.play');
    Route::post('/kahoot/answer', [KahootController::class, 'answer'])->name('kahoot.answer');
    Route::get('/activities/{id}/matching/play', [MatchingController::class, 'play'])->where(['id' => '[1-9][0-9]*'])->name('matching.play');
    Route::post('/matching/answer', [MatchingController::class, 'answer'])->name('matching.answer');
    Route::get('/activities/{id}/wordsearch/play', [WordsearchController::class, 'play'])->where(['id' => '[1-9][0-9]*'])->name('wordsearch.play');
    Route::post('/wordsearch/answer', [WordsearchController::class, 'answer'])->name('wordsearch.answer');
    Route::get('/activities/{id}/roulette/play', [RouletteController::class, 'play'])->where(['id' => '[1-9][0-9]*'])->name('roulette.play');
    Route::post('/roulette/spin', [RouletteController::class, 'spin'])->name('roulette.spin');
    Route::post('/roulette/answer', [RouletteController::class, 'answer'])->name('roulette.answer');
    Route::post('/activities/{id}/start', [ParticipationController::class, 'start'])->where(['id' => '[1-9][0-9]*'])->name('participation.start');
    Route::post('/activities/{id}/finish', [ParticipationController::class, 'finish'])->where(['id' => '[1-9][0-9]*'])->name('participation.finish');
    Route::post('/activities/{id}/abandon', [ParticipationController::class, 'abandon'])->where(['id' => '[1-9][0-9]*'])->name('participation.abandon');
    Route::get('/activities/{id}/result', [ParticipationController::class, 'result'])->where(['id' => '[1-9][0-9]*'])->name('participation.result');
    Route::post('/activities/{id}/expire', [ParticipationController::class, 'expire'])->where(['id' => '[1-9][0-9]*'])->name('participation.expire');
});
