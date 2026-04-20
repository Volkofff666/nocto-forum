<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Settings/Edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:32|regex:/^[a-zA-Z0-9_]+$/|unique:users,username,' . $user->id,
            'bio'        => 'nullable|string|max:500',
            'avatar_url' => 'nullable|url|max:500',
            'cover_url'  => 'nullable|url|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Профиль обновлён');
    }
}
