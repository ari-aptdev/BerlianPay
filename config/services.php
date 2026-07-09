<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Provider WhatsApp untuk reminder (fase lanjutan setelah email stabil)
    'whatsapp' => [
        'provider' => env('WA_PROVIDER', 'fonnte'),
        'api_url' => env('WA_API_URL', 'https://api.fonnte.com/send'),
        'api_key' => env('WA_API_KEY'),
    ],

];
