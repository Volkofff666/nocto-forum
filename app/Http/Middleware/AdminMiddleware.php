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

        // Fixed: previously the isModerator() check always ran after the admin branch,
        // causing admins without a separate moderator flag to be rejected (403).
        // Now the two levels are strictly separated — admin branch returns early.
        if ($level === 'admin') {
            if (!$user->isAdmin()) {
                abort(403, 'Только для администраторов.');
            }
            return $next($request);
        }

        if (!$user->isModerator()) {
            abort(403, 'Доступ запрещён.');
        }

        return $next($request);
    }
}
