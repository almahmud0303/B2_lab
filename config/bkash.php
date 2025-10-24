<?php

return [
    /*
    |--------------------------------------------------------------------------
    | bKash Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for bKash payment gateway integration
    |
    */

    'app_key' => env('BKASH_APP_KEY', 'sandboxTokenizedAppKey'),
    'app_secret' => env('BKASH_APP_SECRET', 'sandboxTokenizedAppSecret'),
    'username' => env('BKASH_USERNAME', 'sandboxTokenizedUser'),
    'password' => env('BKASH_PASSWORD', 'sandboxTokenizedPass'),
    
    // bKash API URLs
    'sandbox' => env('BKASH_SANDBOX', true),
    'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
    'grant_token_url' => env('BKASH_GRANT_TOKEN_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant'),
    'payment_url' => env('BKASH_PAYMENT_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/payment/create'),
    'execute_url' => env('BKASH_EXECUTE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/payment/execute'),
    'query_url' => env('BKASH_QUERY_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/payment/query'),
    
    // Callback URLs
    'callback_url' => env('BKASH_CALLBACK_URL', env('APP_URL') . '/payment/callback'),
    'cancel_url' => env('BKASH_CANCEL_URL', env('APP_URL') . '/payment/cancel'),
    'fail_url' => env('BKASH_FAIL_URL', env('APP_URL') . '/payment/fail'),
];
