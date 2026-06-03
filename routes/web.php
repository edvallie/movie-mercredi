<?php

use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PollController::class, 'create'])->name('poll.create');
Route::post('/polls', [PollController::class, 'store'])->name('poll.store');
Route::get('/poll/{slug}', [PollController::class, 'show'])->name('poll.show');
Route::post('/poll/{slug}/vote', [VoteController::class, 'store'])->name('poll.vote');
Route::get('/poll/{slug}/results', [VoteController::class, 'results'])->name('poll.results');
