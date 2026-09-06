<?php

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
