<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram' => [
        'client_id'     => env('TELEGRAM_BOT_TOKEN'),
        'client_secret' => env('TELEGRAM_BOT_TOKEN'),
        'redirect'      => env('TELEGRAM_REDIRECT_URI'),
        'bot_token'     => env('TELEGRAM_BOT_TOKEN'),
        'channel'       => env('TELEGRAM_CHANNEL', '@noctoproxy'),
    ],

];
