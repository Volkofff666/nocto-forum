<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminLogger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $role   = $request->input('role');

        $users = User::withCount('articles')
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('username', 'ilike', "%{$search}%"))
            ->when($role === 'banned', fn($q) => $q->whereNotNull('banned_at'))
            ->when($role && $role !== 'banned', fn($q) => $q->where('role', $role))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users'   => $users,
            'filters' => ['search' => $search, 'role' => $role],
        ]);
    }

    public function show(User $user): Response
    {
        $user->loadCount(['articles', 'comments']);

        $articles = $user->articles()->latest()->limit(10)->get(['id', 'title', 'slug', 'status', 'votes_count', 'views_count', 'created_at']);
        $comments = $user->comments()->with('article:id,title,slug')->latest()->limit(10)->get();

        return Inertia::render('Admin/UserDetail', [
            'user'     => $user,
            'articles' => $articles,
            'comments' => $comments,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Нельзя изменить свою роль.']);
        }

        $request->validate(['role' => 'required|in:user,moderator,admin']);
        $user->update(['role' => $request->role]);

        AdminLogger::log('change_role', "Роль @{$user->username} изменена на {$request->role}", 'user', $user->id);

        return back()->with('success', "Роль пользователя {$user->name} обновлена.");
    }

    public function ban(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['ban' => 'Нельзя забанить самого себя.']);
        }

        $request->validate(['reason' => 'nullable|string|max:255']);

        $user->update([
            'banned_at'  => now(),
            'ban_reason' => $request->reason,
        ]);

        AdminLogger::log('ban', "Пользователь @{$user->username} заблокирован. Причина: " . ($request->reason ?? 'не указана'), 'user', $user->id);

        return back()->with('success', "Пользователь {$user->name} заблокирован.");
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null, 'ban_reason' => null]);

        AdminLogger::log('unban', "Пользователь @{$user->username} разблокирован", 'user', $user->id);

        return back()->with('success', "Пользователь {$user->name} разблокирован.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Нельзя удалить самого себя.']);
        }

        $name = $user->name;
        $username = $user->username;
        $user->delete();

        AdminLogger::log('delete_user', "Пользователь @{$username} удалён", 'user', null);

        return back()->with('success', "Пользователь {$name} удалён.");
    }
}
