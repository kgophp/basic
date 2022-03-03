<?php

return [

    'directory' => app_path('Admin'),

    //Token effective days
    'passport_token_ttl' => env('PASSPORT_TOKEN_TTL', 1),

    //Refresh token valid days
    'passport_refresh_token_ttl' => env('PASSPORT_REFRESH_TOKEN_TTL', 7),

    'super_admin' => [
        'provider' => env('SUPER_ADMIN_PROVIDER', 'admin'),

        'auth' => env('SUPER_ADMIN_AUTH', 'auth:admin'),

        'guard' => env('SUPER_ADMIN_GRARD', 'admin')
    ],

    'multi_auth_guards' => env('MULTI_AUTH_GUARDS'),

    'route' => [
        'prefix' => 'api',
        'namespace' => 'App\\Admin\\Controllers',
        'middleware' => ['api', 'auth:admin','admin.permission','admin.operationLog'],
        'multi_middleware' => ['api', 'auth:admin','admin.permission','admin.operationLog'],
    ],
    
    'operation_log' => [
        'operation_log_table'    => 'admin_operation_log',
        'enable' => true,

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs.
         */
        'except' => [
            'admin/auth/logs*',
        ],
    ],

];
