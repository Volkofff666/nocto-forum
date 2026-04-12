<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('telegram')->redirect();
    }

    public function callback()
    {
        $telegramUser = Socialite::driver('telegram')->user();

        $user = User::where('telegram_id', $telegramUser->getId())->first();

        if (!$user) {
            $username = $this->generateUsername($telegramUser->getNickname() ?? $telegramUser->getName());

            $user = User::create([
                'name'        => $telegramUser->getName(),
                'username'    => $username,
                'telegram_id' => $telegramUser->getId(),
                'avatar_url'  => $telegramUser->getAvatar(),
                'password'    => null,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect('/');
    }

    private function generateUsername(string $base): string
    {
        $username = preg_replace('/[^a-z0-9_]/i', '', $base);
        $username = strtolower($username) ?: 'user';

        if (!User::where('username', $username)->exists()) {
            return $username;
        }

        do {
            $candidate = $username . rand(1000, 9999);
        } while (User::where('username', $candidate)->exists());

        return $candidate;
    }
}
