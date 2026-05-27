<?php

return [ 
    'subscription_activation_mail_master' => [
        'subject' => 'Your Subscription is Activated',
        'body' => [
            'line1' => 'Hello :user_name,',
            'line2' => "Thank you for subscribing to our <strong>:plan_name</strong>.",
            'line3' => '<strong>Billing Cycle</strong>: :billing_cycle',
            'line4' => '<strong>Start Date</strong>: :start_date',
            'line5' => '<strong>End Date</strong>: :end_date',
            'line6' => '<strong>Amount Paid</strong>: :price',
            'line7' => 'Your subscription is now active. Enjoy all the features included in your plan!',
        ]
    ],
    'subscription_activation_mail_super_admin' => [
        'subject' => 'New Subscription Purchased',
        'body' => [
            'line1' => 'Hello,',
            'line2' => "A new subscription has been purchased by:",
            'line3' => '<strong>Name:</strong> :user_name',
            'line4' => '<strong>Email:</strong> :user_email',
            'line5' => '<strong>Mobile:</strong> :phone_number',
            'line6' => '<strong>Plan Name:</strong> :plan_name',
            'line7' => '<strong>Billing Cycle:</strong> :billing_cycle',
            'line8' => '<strong>Amount Paid:</strong> :price',
            'line9' => '<strong>Start Date:</strong> :start_date',
            'line10' => '<strong>End Date:</strong> :end_date',
        ]
    ],

    'subscription_plan_deactivated_master' => [
        'subject' => 'Your subscription plan has been deactivated',
        'body' => [
            'line1' => 'Hello :user_name',
            'line2' => "We would like to inform you that your current subscription plan <strong>:plan_name</strong> has been deactivated by the administrator.",
            'line3' => 'You can continue using the plan until <strong>:end_date</strong>, but it will not auto-renew. Please choose a new plan to continue without interruption.',
        ]
    ],

    'subscription_plan_activated_master' => [
        'subject' => 'Good news! Your subscription plan is active again',
        'body' => [
            'line1' => 'Hello :user_name',
            'line2' => 'We\'re excited to let you know that your subscription plan <strong>:plan_name</strong> has been reactivated by the administrator.',
            'line3' => 'If your subscription expired earlier, you can purchase this plan again from your dashboard.',
        ]
    ],


    'event_status_change_by_admin' => [
        'subject_approved' => 'Your Event Has Been Approved',
        'subject_rejected' => 'Your Event Submission Was Not Approved',
        'body' => [
            'line1' => 'Hello :user_name',
            'approved' => [
                'line1' => 'We\'re happy to inform you that your event “:event_name” has been successfully reviewed and approved by our admin team.',
                'line2' => 'Event Details',
                'line2_a' => 'Event Name',
                'line2_b' => 'Event Date & Time',
                'line2_c' => 'Location',
                'line3' => 'Your event is now live and visible to members. You can manage attendees and view event details from your dashboard.',
                'line4' => 'If you have any questions or need assistance, feel free to contact our support team.',
            ],
            'rejected' => [
                'line1' => 'Thank you for submitting your event “:event_name” for review',
                'line2' => 'After careful evaluation, we regret to inform you that the event was not approved at this time.',

                'line3' => 'If you have any questions or need assistance, feel free to contact our support team.',
            ]
        ]
    ],
    'master_subscription_cancelled_mail' => [
        'subject' => 'Your subscription has been cancelled',
        'body' => [
            'line1' => 'Hello :name',
            'line2' => 'Your Velvet Room subscription has expired because it was cancelled and has now reached the end of its active period.',
            'line3' => 'You will no longer have access to premium features, but you can resubscribe at any time through the app.',
            'line4' => 'Thank you for being a part of Velvet Room. We hope to see you again soon.',
        ]
    ],

    'regards'   => 'Regards',
    'project_name' => 'Exklusive IOS Community App',
];

?>