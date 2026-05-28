<?php

return [
    'default' => [
        'logo' => 'default/logo.png',
        'short_logo' => 'default/short_logo.png',
        'favicon' => 'default/favicon.png',
        'no_image' => 'default/no-image.jpg',
        'staff-image' => 'default/staff-img.png',
        'building-image' => 'default/building-image.png',
        'help_pdf' => 'default/help_pdf.pdf',
        'user_icon' => 'default/user-icon.svg',
        'datatable_loader' => 'default/datatable_loader.gif',
        'group_icon' => 'images/groupIcon.svg',
        'firebase_json_file' => storage_path('app/firebase-auth.json'),
        'page_loader' => 'default/page-loader.gif',
    ],
    'profile_max_size' => 2048,
    'profile_max_size_in_mb' => '2MB',


    'roles' => [
        'super_admin' => 1,
        'customer' => 2,
    ],

    'approval_status' => [
        '0' => 'Pending',
        '1' => 'Approved',
        '2' => 'Rejected'
    ],
    
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],

    "login_type" => [
        'google' => 'Google',
        'normal' => 'Normal',
        'apple' => 'Apple',
        'facebook' => 'Facebook'
    ],

    'date_format' => [
        'date' => 'd M Y',
        'time' => 'h:i A',
        'date_time' => 'd M Y, h:i A'
    ],

    'search_date_format' => [ //$whereFormat = '%m/%d/%Y %h:%i %p';
        'date' => '%d %b %Y',
        'time' => '%h:%i %p',
        'date_time' => '%d %b %Y, %h:%i %p'
    ],

    'js_date_format' => [
        'date' => 'dd M yyyy',           // e.g., 19 May 2025
        'time' => 'hh:ii A',             // Requires timepicker support
        'date_time' => 'dd M yyyy, hh:ii A'
    ],

    'moment_js_date_format' => [
        'date' => 'DD MMM YYYY',           // e.g., 19 May 2025
        'time' => 'hh:mm A',             // Requires timepicker support
        'date_time' => 'DD MMM YYYY, hh:mm A'
    ],

    // 'currency_symbol' => '₹',
    'currency_symbol' => '',

    'languages' => [
        'en' => 'English'
    ],

    'api_per_page' => 10,
    'api_ledger_per_page' => 20,
    'customer_per_page' => 20,
    'deleted_receipt_days' => 30,
    'default_country_code' => '91',
];
