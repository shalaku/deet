<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Set the secret key used to sign your tokens. This key should be set in
    | your .env file.
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Time To Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 60 minutes.
    |
    */

    'ttl' => env('JWT_TTL', 60), // 60 minutes

    /*
    |--------------------------------------------------------------------------
    | Refresh Time To Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. Defaults to 20160 minutes (14 days).
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // 14 days

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | In order to invalidate tokens, the blacklist functionality needs to be
    | enabled. If you plan on using this, then enable it.
    |
    */

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | JWT Claims
    |--------------------------------------------------------------------------
    |
    | Specify the JWT claims that will be present in the token.
    |
    */

    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],

    'optional_claims' => [
        // 'foo',
        // 'bar',
    ],
    // Other configurations remain default...

];
