<?php

use App\Http\Controllers\MatchingController;
use App\Http\Controllers\WordsearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('teacher')->name('teacher.word-search.')->group(function () {
    Route::get('activities/{id}/word-search/configure', [WordsearchController::class, 'configure'])->name('configure');
    Route::post('activities/{id}/word-search', [WordsearchController::class, 'store'])->name('store');
    Route::get('activities/{id}/word-search/edit', [WordsearchController::class, 'edit'])->name('edit');
    Route::put('activities/{id}/word-search', [WordsearchController::class, 'update'])->name('update');
});

Route::get('student/activities/{id}/word-search/play', [WordsearchController::class, 'play'])->name('student.word-search.play');
Route::post('student/word-search/answer', [WordsearchController::class, 'answer'])->name('student.word-search.answer');

Route::prefix('teacher')->name('teacher.matching.')->group(function () {
    Route::get('activities/{id}/matching/configure', [MatchingController::class, 'configure'])->name('configure');
    Route::post('activities/{id}/matching', [MatchingController::class, 'store'])->name('store');
    Route::get('activities/{id}/matching/edit', [MatchingController::class, 'edit'])->name('edit');
    Route::put('activities/{id}/matching', [MatchingController::class, 'update'])->name('update');
});

Route::get('student/activities/{id}/matching/play', [MatchingController::class, 'play'])->name('student.matching.play');
Route::post('student/matching/answer', [MatchingController::class, 'answer'])->name('student.matching.answer');