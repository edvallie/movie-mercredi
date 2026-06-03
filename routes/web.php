<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PollController::class, 'create'])->name('poll.create');
Route::post('/polls', [PollController::class, 'store'])->name('poll.store');
Route::get('/poll/{slug}', [PollController::class, 'show'])->name('poll.show');
Route::post('/poll/{slug}/vote', [VoteController::class, 'store'])->name('poll.vote');
Route::get('/poll/{slug}/results', [VoteController::class, 'results'])->name('poll.results');

Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/poll/{slug}/admin', [AdminController::class, 'results'])->name('admin.results');
    Route::delete('/poll/{slug}/admin/votes/{voteId}', [AdminController::class, 'deleteVote'])->name('admin.vote.delete');
});
