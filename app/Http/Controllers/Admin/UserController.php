<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $users = User::withCount('articles')
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('username', 'ilike', "%{$search}%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users'   => $users,
            'filters' => ['search' => $search],
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        // Protect admin account from demoting itself
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Нельзя изменить свою роль.']);
        }

        $request->validate(['role' => 'required|in:user,moderator,admin']);
        $user->update(['role' => $request->role]);

        return back()->with('success', "Роль пользователя {$user->name} обновлена.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Нельзя удалить самого себя.']);
        }

        $user->delete();
        return back()->with('success', "Пользователь удалён.");
    }
}
