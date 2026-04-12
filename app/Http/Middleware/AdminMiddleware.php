<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, string $level = 'moderator'): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($level === 'admin' && !$user->isAdmin()) {
            abort(403, 'Только для администраторов.');
        }

        if (!$user->isModerator()) {
            abort(403, 'Доступ запрещён.');
        }

        return $next($request);
    }
}
