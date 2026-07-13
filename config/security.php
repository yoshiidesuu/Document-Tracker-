<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */

    'headers' => [
        'x-frame-options' => env('SEC_HEADER_X_FRAME_OPTIONS', 'DENY'),
        'x-content-type-options' => env('SEC_HEADER_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'x-xss-protection' => env('SEC_HEADER_X_XSS_PROTECTION', '1; mode=block'),
        'referrer-policy' => env('SEC_HEADER_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'permissions-policy' => env('SEC_HEADER_PERMISSIONS_POLICY', 'geolocation=(), microphone=(), camera=(self), payment=(), usb=(), magnetometer=(), accelerometer=(), gyroscope=()'),
        'hsts' => [
            'enabled' => env('SEC_HSTS_ENABLED', true),
            'max-age' => env('SEC_HSTS_MAX_AGE', 31536000),
            'include-sub-domains' => env('SEC_HSTS_INCLUDE_SUB_DOMAINS', true),
            'preload' => env('SEC_HSTS_PRELOAD', false),
        ],
        'content-security-policy' => env('SEC_CSP', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; media-src 'self' blob:; frame-src 'none'; object-src 'none'; base-uri 'self'; form-action 'self'"),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy (NIST SP 800-63 / OWASP)
    |--------------------------------------------------------------------------
    */

    'password' => [
        'min_length' => (int) env('SEC_PASSWORD_MIN_LENGTH', 12),
        'require_uppercase' => env('SEC_PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('SEC_PASSWORD_REQUIRE_LOWERCASE', true),
        'require_number' => env('SEC_PASSWORD_REQUIRE_NUMBER', true),
        'require_symbol' => env('SEC_PASSWORD_REQUIRE_SYMBOL', true),
        'max_age_days' => (int) env('SEC_PASSWORD_MAX_AGE_DAYS', 90),
        'history_count' => (int) env('SEC_PASSWORD_HISTORY_COUNT', 5),
        'expiry_warning_days' => (int) env('SEC_PASSWORD_EXPIRY_WARNING_DAYS', 14),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication & Session Security (OWASP ASVS / ISO 27001)
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'max_login_attempts' => (int) env('SEC_AUTH_MAX_ATTEMPTS', 5),
        'lockout_time_minutes' => (int) env('SEC_AUTH_LOCKOUT_MINUTES', 15),
        'lockout_increment' => env('SEC_AUTH_LOCKOUT_INCREMENT', true),
        'session_timeout_minutes' => (int) env('SESSION_LIFETIME', 30),
        'session_expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', true),
        'session_encrypt' => env('SESSION_ENCRYPT', true),
        'session_http_only' => env('SESSION_HTTP_ONLY', true),
        'session_same_site' => env('SESSION_SAME_SITE', 'strict'),
        'session_secure' => env('SESSION_SECURE_COOKIE', true),
        'remember_me_days' => (int) env('SEC_AUTH_REMEMBER_ME_DAYS', 7),
        'require_email_verification' => env('SEC_AUTH_REQUIRE_EMAIL_VERIFICATION', true),
        'enforce_mfa' => env('SEC_AUTH_ENFORCE_MFA', false),
        'password_reset_expire_minutes' => (int) env('SEC_AUTH_PASSWORD_RESET_EXPIRE', 15),
        'password_reset_throttle_seconds' => (int) env('SEC_AUTH_PASSWORD_RESET_THROTTLE', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (OWASP A04 / DICT)
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        'web' => [
            'max_attempts' => (int) env('SEC_RATE_LIMIT_WEB_MAX', 60),
            'decay_minutes' => (int) env('SEC_RATE_LIMIT_WEB_DECAY', 1),
        ],
        'api' => [
            'max_attempts' => (int) env('SEC_RATE_LIMIT_API_MAX', 120),
            'decay_minutes' => (int) env('SEC_RATE_LIMIT_API_DECAY', 1),
        ],
        'login' => [
            'max_attempts' => (int) env('SEC_RATE_LIMIT_LOGIN_MAX', 5),
            'decay_minutes' => (int) env('SEC_RATE_LIMIT_LOGIN_DECAY', 1),
        ],
        'password_reset' => [
            'max_attempts' => (int) env('SEC_RATE_LIMIT_PASSWORD_RESET_MAX', 3),
            'decay_minutes' => (int) env('SEC_RATE_LIMIT_PASSWORD_RESET_DECAY', 15),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation (OWASP A03 - Injection Prevention)
    |--------------------------------------------------------------------------
    */

    'input_validation' => [
        'sanitize_all_inputs' => env('SEC_SANITIZE_ALL_INPUTS', true),
        'strip_script_tags' => env('SEC_STRIP_SCRIPT_TAGS', true),
        'encode_special_chars' => env('SEC_ENCODE_SPECIAL_CHARS', true),
        'max_input_length' => (int) env('SEC_MAX_INPUT_LENGTH', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit & Logging (ISO 27001 / DICT / OWASP A09)
    |--------------------------------------------------------------------------
    */

    'audit' => [
        'enabled' => env('SEC_AUDIT_ENABLED', true),
        'log_all_requests' => env('SEC_AUDIT_LOG_REQUESTS', false),
        'log_auth_events' => env('SEC_AUDIT_LOG_AUTH', true),
        'log_model_changes' => env('SEC_AUDIT_LOG_MODEL_CHANGES', true),
        'log_failed_logins' => env('SEC_AUDIT_LOG_FAILED_LOGINS', true),
        'log_data_exports' => env('SEC_AUDIT_LOG_DATA_EXPORTS', true),
        'retention_days' => (int) env('SEC_AUDIT_RETENTION_DAYS', 365),
        'channel' => env('SEC_AUDIT_CHANNEL', 'security'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption & Data Protection (OWASP A02 / ISO 27001 / DICT)
    |--------------------------------------------------------------------------
    */

    'encryption' => [
        'cipher' => env('SEC_ENCRYPTION_CIPHER', 'AES-256-CBC'),
        'encrypt_pii' => env('SEC_ENCRYPT_PII', true),
        'encrypt_database_columns' => env('SEC_ENCRYPT_DB_COLUMNS', true),
        'key_rotation_days' => (int) env('SEC_ENCRYPTION_KEY_ROTATION_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security (OWASP)
    |--------------------------------------------------------------------------
    */

    'uploads' => [
        'max_file_size' => (int) env('SEC_UPLOAD_MAX_SIZE', 10485760),
        'allowed_extensions' => env('SEC_UPLOAD_ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv'),
        'scan_for_malware' => env('SEC_UPLOAD_SCAN_MALWARE', true),
        'store_outside_webroot' => env('SEC_UPLOAD_STORE_OUTSIDE_WEBROOT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Security (OWASP)
    |--------------------------------------------------------------------------
    */

    'cors' => [
        'allowed_origins' => explode(',', (string) env('SEC_CORS_ALLOWED_ORIGINS', env('APP_URL', 'http://localhost'))),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allow_credentials' => env('SEC_CORS_ALLOW_CREDENTIALS', true),
        'max_age' => (int) env('SEC_CORS_MAX_AGE', 86400),
    ],

    /*
    |--------------------------------------------------------------------------
    | DICT (Philippines Data Privacy Act) Compliance
    |--------------------------------------------------------------------------
    */

    'dict' => [
        'data_privacy_officer_email' => env('SEC_DPO_EMAIL', 'dpo@'.(parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost')),
        'breach_notification_enabled' => env('SEC_BREACH_NOTIFICATION', true),
        'breach_notification_email' => env('SEC_BREACH_NOTIFICATION_EMAIL', 'security@'.(parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost')),
        'data_retention_days' => (int) env('SEC_DATA_RETENTION_DAYS', 1825),
        'privacy_notice_url' => env('SEC_PRIVACY_NOTICE_URL', '/privacy'),
        'consent_required' => env('SEC_CONSENT_REQUIRED', true),
        'data_classification' => [
            'public' => env('SEC_DC_PUBLIC', 'Public information'),
            'internal' => env('SEC_DC_INTERNAL', 'Internal use only'),
            'confidential' => env('SEC_DC_CONFIDENTIAL', 'Confidential information'),
            'restricted' => env('SEC_DC_RESTRICTED', 'Highly restricted - PII'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP & Network Security
    |--------------------------------------------------------------------------
    */

    'network' => [
        'blocked_ips' => array_filter(explode(',', (string) env('SEC_BLOCKED_IPS', ''))),
        'whitelist_ips' => array_filter(explode(',', (string) env('SEC_WHITELIST_IPS', ''))),
        'enforce_https' => env('SEC_ENFORCE_HTTPS', true),
        'trust_proxies' => env('SEC_TRUST_PROXIES', '*'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Incident Response
    |--------------------------------------------------------------------------
    */

    'monitoring' => [
        'detect_brute_force' => env('SEC_DETECT_BRUTE_FORCE', true),
        'detect_session_hijacking' => env('SEC_DETECT_SESSION_HIJACKING', true),
        'alert_on_suspicious_activity' => env('SEC_ALERT_SUSPICIOUS', true),
        'alert_email' => env('SEC_ALERT_EMAIL', 'security@'.(parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost')),
        'incident_response_team' => explode(',', (string) env('SEC_INCIDENT_RESPONSE_TEAM', '')),
    ],

];
