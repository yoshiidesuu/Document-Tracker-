<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => explode(',', (string) env('SEC_CORS_ALLOWED_ORIGINS', env('APP_URL', 'http://localhost'))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-XSRF-TOKEN', 'Accept'],
    'exposed_headers' => [],
    'max_age' => (int) env('SEC_CORS_MAX_AGE', 86400),
    'supports_credentials' => env('SEC_CORS_ALLOW_CREDENTIALS', true),
];
