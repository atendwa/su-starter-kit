<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data Service Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration manages endpoints and URLs for the data service.
    |
    */

    'url' => [
        'live' => env('DATA_SERVICE_LIVE_URL', 'https://ams.strathmore.edu/dataservice/'),
        'test' => env('DATA_SERVICE_TEST_URL', 'https://juba.strathmore.edu/dataservice/'),
    ],

    'endpoints' => [
        'departments' => [
            'all' => env('ALL_DEPARTMENTS', 'department/getAllDepartments'),
            'single' => env('DEPARTMENT', 'departments/'),
        ],
        'staff' => [
            'by_username' => env('STAFF_BY_USERNAME', 'staff/getStaffByUsername/'),
            'by_number' => env('STAFF_BY_NUMBER', 'staff/getStaff/'),
            'all' => env('ALL_STAFF', "staff/getAllStaff"),
        ],
        'students' => [
            'all_with_open_accounts' => env('ALL_STUDENTS_WITH_OPEN_ACCOUNTS', "student/getAllStudentsWithOpenAccounts"),
            'all_current' => env('ALL_CURRENT_STUDENTS', "student/getAllCurrentStudents"),
            'single' => env('STUDENT', 'student/getStudent/'),
        ],
    ],
];
