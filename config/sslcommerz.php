<?php

return [
    'store_id' => env('SSLCZ_STORE_ID', ''),
    'store_password' => env('SSLCZ_STORE_PASSWORD', ''),
    'sandbox_mode' => env('SSLCZ_TESTMODE', true),

    'api_domain' => env('SSLCZ_TESTMODE', true) 
        ? 'https://sandbox.sslcommerz.com' 
        : 'https://securepay.sslcommerz.com',
];
