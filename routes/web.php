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

Route::prefix('teacher')->name('teacher.matchings.')->group(function () {
    Route::get('matchings/create', [MatchingController::class, 'create'])->name('create');
    Route::post('matchings', [MatchingController::class, 'store'])->name('store');
});

Route::get('matchings/{matching}', [MatchingController::class, 'show'])->name('matchings.show');
Route::get('matchings/{matching}/play', [MatchingController::class, 'play'])->name('matchings.play');
Route::post('matchings/{matching}/answer', [MatchingController::class, 'answer'])->name('matchings.answer');