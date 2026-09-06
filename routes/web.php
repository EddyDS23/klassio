<?php

use App\Http\Controllers\MatchingController;
use App\Http\Controllers\WordsearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('teacher')->name('teacher.wordsearches.')->group(function () {
    Route::get('wordsearches/create', [WordsearchController::class, 'create'])->name('create');
    Route::post('wordsearches', [WordsearchController::class, 'store'])->name('store');
});

Route::get('wordsearches/{wordsearch}', [WordsearchController::class, 'show'])->name('wordsearches.show');
Route::get('wordsearches/{wordsearch}/play', [WordsearchController::class, 'play'])->name('wordsearches.play');
Route::post('wordsearches/{wordsearch}/answer', [WordsearchController::class, 'answer'])->name('wordsearches.answer');

Route::prefix('teacher')->name('teacher.matchings.')->group(function () {
    Route::get('matchings/create', [MatchingController::class, 'create'])->name('create');
    Route::post('matchings', [MatchingController::class, 'store'])->name('store');
});

Route::get('matchings/{matching}', [MatchingController::class, 'show'])->name('matchings.show');
Route::get('matchings/{matching}/play', [MatchingController::class, 'play'])->name('matchings.play');
Route::post('matchings/{matching}/answer', [MatchingController::class, 'answer'])->name('matchings.answer');