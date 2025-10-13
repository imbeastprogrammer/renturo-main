<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Passport Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Passport will use when
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY', storage_path('oauth-private.key')),

    'public_key' => env('PASSPORT_PUBLIC_KEY', storage_path('oauth-public.key')),

    /*
    |--------------------------------------------------------------------------
    | Client UUIDs
    |--------------------------------------------------------------------------
    |
    | By default, Passport uses auto-incrementing primary keys when assigning
    | IDs to clients. However, if Passport is installed using the provided
    | --uuids switch, this will be set to "true" and UUIDs will be used.
    |
    */

    'client_uuids' => false,

    /*
    |--------------------------------------------------------------------------
    | Personal Access Client
    |--------------------------------------------------------------------------
    |
    | If you enable client hashing, you should set the personal access client
    | ID and unhashed secret within your environment file. The values will
    | get used while issuing fresh personal access tokens to your users.
    |
    */

    'personal_access_client' => [
        'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
        'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
    ],

    'key_path' => env('OAUTH_KEY_PATH', storage_path('')),

    /*
    |--------------------------------------------------------------------------
    | Token Lifetimes
    |--------------------------------------------------------------------------
    |
    | Here you may specify the amount of time that access tokens and refresh
    | tokens should be valid for. This will be used when generating tokens
    | during authentication or token refresh.
    |
    */

    'token_ttl' => env('PASSPORT_TOKEN_TTL', 60), // minutes
    'refresh_token_ttl' => env('PASSPORT_REFRESH_TOKEN_TTL', 20160), // minutes = 14 days

    /*
    |--------------------------------------------------------------------------
    | Token Revocation
    |--------------------------------------------------------------------------
    |
    | Here you may specify whether to revoke refresh tokens when the related
    | access token is revoked. This is typically set to true as it prevents
    | unauthorized access using revoked access tokens.
    |
    */

    'revoke_refresh_tokens_on_logout' => true

];
