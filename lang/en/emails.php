<?php

return [
    'user_register_welcome_mail_customer' => [
        'subject' => 'Welcome to Juste platform',
        'body' => [
            'line1' => 'Hello :user_name,',
            'line2' => "Welcome to the Juste platform! We're excited to have you on board. You can now log in and start exploring qualified professionals near you to access the services you need.",
            'line3' => 'Thank you for joining us!',
        ]
    ],

    'user_register_mail_super_admin' => [
        'subject' => 'A new user has registered',
        'body' => [
            'line1' => 'Hello :name,',
            'line2' => "A new user has just registered on the platform:",
            'line3' => '<strong>Name:</strong> :username',
            'line4' => '<strong>Email:</strong> :userEmail',
            'line5' => '<strong>Role:</strong> :role',
            'line6' => '<strong>Mobile Number:</strong> :phone_number',
            'line7' => 'Please review their profile if needed.',
        ]
    ],

    'reset_password_admin_panel' => [
        'subject' => "Reset Password Notification",
        'body' => [
            'line1' => 'Hello :user_name,',
            'line2' => "You are receiving this email because we received a password reset request for your account.",
            'line3' => "Please click on the link below to reset your password and get access to your account :",
            'line4' => 'If you\'re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:',
            'line5' => 'If you did not request a password reset, no further action is required.',
            "button" => "Reset Password"
        ]
    ],

    'reset_password_mobile_app' => [
        'subject' => "Reset Password OTP",
        'body' => [
            'line1' => 'Hello :user_name,',
            'line2' => "We received a request to reset your password. Please use the following OTP to proceed:",
            'line3' => "Your OTP:",
            'line4' => 'This OTP will expire in :expire_time. If you did not request a password reset, please ignore this email.',
            'line5' => 'If you did not request a password reset, no further action is required.',
        ]
    ],

    'profile_verify_email_otp' => [
        'subject' => "Email Verification OTP",
        'body' => [
            'line1' => 'Hello :user_name,',
            'line2' => "We received a request to verify your email address.",
            'line3' => "Please use the following One-Time Password (OTP) to proceed with verifying your email:",
            'line4' => 'This OTP will expire in :expire_time minutes.',
            'line5' => 'If you did not request a password reset, you can safely ignore this email.',
        ]
    ],

    'regards'   => 'Regards',
    'project_name' => config('app.name'),
];

?>