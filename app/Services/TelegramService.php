<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function getSubscriberCount(): int
    {
        $token   = config('services.telegram.bot_token');
        $channel = config('services.telegram.channel', '@noctoproxy');
        $cacheKey = 'tg_subscribers';

        if (!$token) {
            return Cache::get($cacheKey, 0);
        }

        return Cache::remember($cacheKey, now()->addHour(), function () use ($token, $channel) {
            try {
                $response = Http::timeout(5)->get(
                    "https://api.telegram.org/bot{$token}/getChatMemberCount",
                    ['chat_id' => $channel]
                );

                if ($response->successful() && $response->json('ok')) {
                    return (int) $response->json('result', 0);
                }
            } catch (\Exception $e) {
                Log::warning('Telegram API error: ' . $e->getMessage());
            }

            // Return previously cached value if API fails
            return Cache::get($cacheKey . '_last', 0);
        });
    }
}
