<?php

return [
    /*
   |--------------------------------------------------------------------------
   | Authentication Configuration
   |--------------------------------------------------------------------------
   |
   | This configuration determines the default authentication driver and its options.
   |
   */

    'driver' => env('AUTH_DRIVER', 'ldap'),

    /*
    |--------------------------------------------------------------------------
    | Allow Student Logins
    |--------------------------------------------------------------------------
    |
    | Determine whether student logins are allowed or not.
    |
    */

    'allow_student_logins' => env('ALLOW_STUDENT_LOGINS', true),

    /*
    |--------------------------------------------------------------------------
    | Max Failed Login Attempts
    |--------------------------------------------------------------------------
    |
    | Set the maximum number of failed login attempts allowed.
    |
    */

    'max_login_failures' => env('MAX_LOGIN_FAILURES', 5),

    /*
    |--------------------------------------------------------------------------
    | Authentication Drivers
    |--------------------------------------------------------------------------
    |
    | Define configuration options for different authentication drivers.
    |
    */

    'drivers' => [
        'ldap' => [
            'domain' => [
                'student' => env('STUDENTS_DOMAIN', 'std.strathmore.local'),
                'staff' => env('STAFF_DOMAIN', 'strathmore.local'),
            ],

            'password_reset' => [
                'student' => env('STUDENT_RESET'),
                'staff' => env('STAFF_RESET'),
            ],
            // see lpad config in config/ldap.php
        ],

        'masquerade' => [
            'username' => env('MASQUERADE_USERNAME'),
        ],

        'database' => [
            // Add database authentication configuration if needed
        ],
    ],
];
