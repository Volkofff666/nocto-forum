<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Главная — лента статей
Route::get('/', [ArticleController::class, 'index'])->name('articles.index');

// Статьи
Route::get('/articles/create', [ArticleController::class, 'create'])->middleware('auth')->name('articles.create');
Route::post('/articles', [ArticleController::class, 'store'])->middleware('auth')->name('articles.store');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->middleware('auth')->name('articles.edit');
Route::patch('/articles/{article}', [ArticleController::class, 'update'])->middleware('auth')->name('articles.update');
Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->middleware('auth')->name('articles.destroy');
Route::patch('/articles/{article}/publish', [ArticleController::class, 'publish'])->middleware('auth')->name('articles.publish');

// Комментарии
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth')->name('comments.destroy');

// Голоса
Route::post('/articles/{article}/vote', [VoteController::class, 'store'])->middleware('auth')->name('votes.store');

// Настройки профиля
Route::get('/settings', [SettingsController::class, 'edit'])->middleware('auth')->name('settings.edit');
Route::patch('/settings', [SettingsController::class, 'update'])->middleware('auth')->name('settings.update');

// Мои черновики
Route::get('/my/drafts', [ArticleController::class, 'drafts'])->middleware('auth')->name('articles.drafts');

// Закладки
Route::post('/articles/{article}/bookmark', [BookmarkController::class, 'toggle'])->middleware('auth')->name('bookmarks.toggle');
Route::get('/my/bookmarks', [BookmarkController::class, 'index'])->middleware('auth')->name('bookmarks.index');

// Профиль
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

// Telegram OAuth
Route::get('/auth/telegram', [SocialiteController::class, 'redirect'])->name('auth.telegram');
Route::get('/auth/telegram/callback', [SocialiteController::class, 'callback'])->name('auth.telegram.callback');

// Breeze auth routes
require __DIR__ . '/auth.php';
