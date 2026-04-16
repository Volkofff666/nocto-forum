<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            // Capture ban_reason before logout() nulls out the in-memory user.
            $banReason = Auth::user()->ban_reason ?? 'не указана';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Ваш аккаунт заблокирован. Причина: ' . $banReason;

            // Inertia sends X-Inertia on XHR requests (POST/PATCH/DELETE).
            // A plain redirect() on those returns 302 which Inertia would silently
            // follow without surfacing the error. Return 409 so Inertia reloads
            // the page and the subsequent GET lands on /login with the flash error.
            if ($request->header('X-Inertia')) {
                return response()->json(['message' => $message], 409);
            }

            return redirect()->route('login')
                ->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
