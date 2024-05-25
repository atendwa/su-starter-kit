<?php


return [
    'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),

    'urls' => [
        'sandbox' => env('SANDBOX_URL', 'https://sandbox.safaricom.co.ke'),
        'live' => env('LIVE_URL', 'https://api.safaricom.co.ke'),
    ],

    'credentials' => [
        'consumer' => [
            'key' => env('MPESA_CONSUMER_KEY'),
            'secret' => env('MPESA_CONSUMER_SECRET'),
        ],
        'payment' => [
            'key' => env('MPESA_PAYMENT_CONSUMER_KEY'),
            'secret' => env('MPESA_PAYMENT_CONSUMER_SECRET'),
        ],
    ],

    'endpoints' => [
        'access_token' => env('MPESA_ACCESS_TOKEN', 'oauth/v1/generate?grant_type=client_credentials'),
        'dynamic_qr' => env('MPESA_DYNAMIC_QR_ENDPOINT', 'mpesa/v1/c2b/qrcode/generate'),
        'mpesa_express' => env('MPESA_EXPRESS_ENDPOINT', 'mpesa/stkpush/v1/processrequest'),
    ],

    'callback' => [
        'default' => env('MPESA_DEFAULT_CALLBACK', 'https://tendwa.dev'),
    ],

    'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE'),
    'passkey' => env('MPESA_PASSKEY'),
];
