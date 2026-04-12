<?php

use App\Http\Controllers\SearchController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUsers;
use App\Http\Controllers\Admin\ArticleController as AdminArticles;
use App\Http\Controllers\Admin\CommentController as AdminComments;
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

// Поиск
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Инструменты
Route::get('/tools', [ToolsController::class, 'index'])->name('tools');

// Профиль
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

// Telegram OAuth
Route::get('/auth/telegram', [SocialiteController::class, 'redirect'])->name('auth.telegram');
Route::get('/auth/telegram/callback', [SocialiteController::class, 'callback'])->name('auth.telegram.callback');

// ── Админ-панель ────────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/',          [AdminDashboard::class, 'index'])->name('admin.dashboard');

    // Пользователи
    Route::get('/users',                    [AdminUsers::class, 'index'])->name('admin.users');
    Route::patch('/users/{user}/role',      [AdminUsers::class, 'updateRole'])->middleware('admin:admin')->name('admin.users.role');
    Route::delete('/users/{user}',          [AdminUsers::class, 'destroy'])->middleware('admin:admin')->name('admin.users.destroy');

    // Статьи
    Route::get('/articles',                         [AdminArticles::class, 'index'])->name('admin.articles');
    Route::patch('/articles/{article}/toggle',      [AdminArticles::class, 'toggleStatus'])->name('admin.articles.toggle');
    Route::delete('/articles/{article}',            [AdminArticles::class, 'destroy'])->name('admin.articles.destroy');

    // Комментарии
    Route::get('/comments',              [AdminComments::class, 'index'])->name('admin.comments');
    Route::delete('/comments/{comment}', [AdminComments::class, 'destroy'])->name('admin.comments.destroy');
});

// Breeze auth routes
require __DIR__ . '/auth.php';
