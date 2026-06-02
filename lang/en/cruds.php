<?php

return [
    'menus' => [
        'dashboard' => 'Dashboard',
        'user'      => 'Users',
        'setting'   => 'Settings',
        'staff'     => "System Users",
        'role'      => "Roles",
        'logout'    => "logout"
    ],

    'datatable' => [
        'show'    => 'Show',
        'entries' => 'entries',
        'showing' => 'Showing',
        'to'      => 'to',
        'of'      => 'of',
        'search'  => 'Search',
        'previous' => 'Previous',
        'next'     => 'Next',
        'first'    => 'First',
        'last'     => 'Last',
        'data_not_found' => 'Data not found',
        'processing'     => '',
        'records'   => "records",
    ],

    'dashboard' =>  [
        'title'             => 'Dashboard',
        'title_singular'    => 'Dashabord',
        'super_admin_password' => "Super Admin Password",
        'print_export_this_year_cash_sales' => "This Year Cash & Sales",
    ],


    'setting' => [
        'title' => 'Settings',
        'add_message_subject' => 'Add Message Subject',
        'title_singular' => 'Setting',
        'site'           => 'Site',
        'content'        => 'Content',
        'support'        => 'Support',
        'site_setting'   => 'Site Setting',
        'support_setting' => 'Support Setting',
        'content_setting' => 'Content Setting',
    ],

    'role' => [
        'title'          => 'Role List',
        'title_singular' => 'Role',
        'fields' => [
            'name'        => 'Name',
            'permission' => 'Permissions',
            'created_at'  => 'Created At',
        ],
    ],

    'user'           => [
        'title'          => 'Users',
        'title_singular' => 'User',
        'fields'         => [
            'id'                => 'ID',
            'name'              => 'Name',
            'email'             => 'Email',
            'phone'             => 'Phone',
            'password'          => 'Password',
            'confirm_password'  => 'Confirm Password',
            "profile_image"     => "Profile Image",
            'status'            => 'Status',
            'created_at'        => 'Created Date',
            'updated_at'        => 'Updated',
            'deleted_at'        => 'Deleted',
        ],
    ],

    'staff'           => [
        'title'          => 'System Users',
        'title_singular' => 'System User',
        'fields'         => [
            'id'                => 'ID',
            'name'              => 'Name',
            'email'             => 'Email',
            'phone'             => 'Phone',
            'password'          => 'Password',
            'confirm_password'  => 'Confirm Password',
            'role'              => 'Role',
            'status'            => 'Status',
            'created_at'        => 'Created Date',
            'updated_at'        => 'Updated',
            'deleted_at'        => 'Deleted',
        ],
    ],


// ------------------------------------------------  APIs FIELDS  ----------------------------------------------

    'api' => [
        
    ]

];
