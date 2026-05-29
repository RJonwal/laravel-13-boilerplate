<?php

return [
    'otp_sent' => 'OTP sent successfully',
    'otp_resend' => 'OTP resent successfully',
    'otp_failed' => 'Failed to send OTP. Please try again.',
    'otp_expired' => 'OTP expired. Please request a new one.',
    'otp_invalid' => 'Invalid OTP.',
    'otp_verified' => 'OTP verified successfully.',
    'otp_max_attempts' => 'Maximum OTP attempts reached.',
    'otp_cooldown' => 'Please wait :seconds seconds before requesting another OTP.',
    'user_not_found' => 'User not found.',    
    'step1_completed' => 'Step 1 completed. Proceed to venue info.',
    'step2_completed' => 'Step 2 completed. Proceed to set password.',
    // 'registration_completed' => 'Host registration completed successfully.',
    'registration_failed' => 'Registration failed. Please try again later.',
    'incomplete_steps' => 'Incomplete registration steps.',
    'validation_error' => 'Validation failed: :message',
    'something_went_wrong' => 'Something went wrong. Please try again.',
    'unauthorized' => 'You are not authorized to perform this action.',
    'unauthenticated' => 'You must be logged in to perform this action.',
    
    'profile_retrieved' => 'Profile retrieved successfully.',
    'profile_updated' => 'Profile updated successfully.',
    'profile_update_failed' => 'Failed to update profile. Please try again.',

    'only_host_has_permission' => 'Only a host is authorized to perform this action.',
    'user_blocked_successfully' => 'User has been blocked successfully.',
    'user_unblocked_successfully' => 'User has been unblocked successfully.',
    'user_already_blocked' => 'User is already blocked.',
    'member_blocked' => 'This member is blocked.',


    'invalid_status_value' => 'Invalid status value.',
    'account_deleted_successfully' => 'Account has been deleted successfully.',

    'account_paused' => 'Your account has been paused.',
    'account_unpaused' => 'Your account is now active.',
    
    'host' => [
        'registration_completed' => 'Your profile has been submitted to the admin. Please wait for approval.',
        'profile_retrieved' => 'Profile retrieved successfully.',
        'profile_updated' => 'Profile updated successfully.',
        'profile_update_failed' => 'Failed to update profile. Please try again.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],
    'member' => [
        'registration_completed' => 'Member registration completed successfully.',
        'profile_retrieved' => 'Profile retrieved successfully.',
        'profile_updated' => 'Profile updated successfully.',
        'profile_update_failed' => 'Failed to update profile. Please try again.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],
    'registration' => [
        'step1_notcomplete' => 'Incomplete registration step 1.',
        'step2_host' => 'Step 2 is only required for host registration.',
        'complete_venue_step' => "Please complete venue information (step 2) for host registration."
    ],
    'validation' => [
        'name' => [
            'required' => 'Name is required.',
            'regex' => 'Name should contain only letters and spaces.',
        ],
        'email' => [
            'required' => 'Email is required.',
            'email' => 'Please provide a valid email address.',
            'unique' => 'This email is already taken.',
        ],
        'phone' => [
            'required' => 'Phone number is required.',
            'regex' => 'Phone number must be in international format (e.g., +1234567890).',
            'unique' => 'This phone number is already registered.',
        ],
        'instagram_handle' => [
            'required' => 'Instagram handle is required.',
        ],
        'venue_name' => [
            'required' => 'Venue name is required.',
        ],
        'venue_type' => [
            'required' => 'Venue type is required.',
            'integer' => 'Venue type must be a valid number.',
            'min' => 'Please select a valid venue type.',
        ],
        'venue_description' => [
            'required' => 'Venue description is required.',
            'max' => 'Venue description cannot exceed 1000 characters.',
        ],
    ],
    
    'login' => [
        'login_successful' => 'Login successful',
        'invalid_credentials' => 'Invalid email or password',
        'account_inactive' => 'Your account is inactive. Please contact support.',
        'unauthorized_role' => 'You are not authorized to access this application.',
        'approval_pending' => 'Your account is pending approval. Please wait for confirmation.',
        'approval_rejected' => 'Your profile has been denied. Please contact support for assistance.',
        'approval_required' => 'Account approval is required to login.',
        "login_failed" => 'Login failed. Please try again later.',
    ],   
    'logout' => [
        'logout_successful' => 'Logged out successfully',
        'logout_failed' => 'Logout failed. Please try again later.',
    ],
    'venue_types_retrieved' => 'Venue types retrieved successfully.',
    'privacy_terms_retrieved' => 'Privacy and Terms retrieved successfully.',  
    'early_access_retrieved' => 'Early Access settings retrieved successfully',
    'change_password' => [
        'change_password' => 'Change Password',
        'fields' => [
            'old_password' => 'Old Password',
            'new_password' => 'New Password',
            'confirm_new_password' => 'Confirm New Password',
        ],    
        'messages' => [
            'update_password' => [
                'validation' => [
                    'regex' => 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
                    'confirm' => 'The confirm new password does not match.',
                    'invalid_old_password' => 'The old password you entered is incorrect.',
                    'old_required' => 'The old password field is required.',
                ],
                'success_update' => 'Your password has been updated successfully.',
            ],
        ],
    ],
    'event_creation_failed' => 'Event creation failed. Please try again later.',
    'event_created' => 'Event created successfully.',
    'event_created_under_review' => 'Event successfully created — it will be reviewed shortly.',
    'event_updated' => 'Event updated successfully.',
    'event_time_conflict_other' => 'This member already has another event within :time_gap minutes of this event.',
    'event_time_conflict_self' => 'You already have another event within :time_gap minutes of this event.',
    'member_invited' => 'Member invited successfully.',
    'event_has_passed' => 'The event has already passed. You can not edit this event.',
    'event_has_passed_accept_decline' => 'The event has already passed. You can not accept or decline this event.',
    'event_has_not_completed' => 'The event has not completed yet. You can not review this event.',
    'complete_step_1' => 'Please complete step 1 first',
    'profile_email_otp_send_fail' => 'Failed to send OTP to your email',
    'invalid_expire_otp' => 'The OTP is invalid or has expired',
    'profile_email_updated' => 'Email updated successfully',
    'failed_otp_verify' => 'Failed to verify OTP', 
    'failed_otp_send' => 'Failed to send OTP on phone', 
    'otp_sent_to_phone' => 'OTP sent to your phone successfully', 
    'profile_phone_updated' => 'Phone number updated successfully', 
    'otp_send_email' => 'OTP sent to your email successfully', 
    
    
    'pre_content_added' => 'Pre-content added successfully.',
    'event_content_updated' => 'Event content updated successfully.',
    
    'event_not_found' => 'Event not found.',
    'event_content_not_found' => "Content not found",
    'event_member_not_found' => 'Member not found.',
    'event_member_not_pending' => 'This member does not have a pending status.',
    'event_status_not_permission' => "You do not have permission to update this member's status",
    
    'event_member_status_updated' => 'The member status updated succesfully.',

    'accepted_event' => 'You Accepted the Event',
    'rejected_event' => 'You Rejected the Event',

    'event_content_delete' => 'The event content deleted succesfully.',
    
    'event_invite_request_canceled' => 'The event invite request canceled successfully.',
    'event_review_submitted' => 'The review submitted successfully.',
    'review_already_exist' => 'You have already submitted your review for this event',

    'event_create_limit' => "You've reached your event creation limit. Please upgrade your plan.",
    'event_member_limit' => "You cannot invite members with the basic plan. Please upgrade your plan to continue.",
    'event_invite_request_submitted' => "Invite request submitted successfully for event",
    'event_member_exists' => "You have already invited for this event",
    'no_active_subscription' => "You haven't subscribed to any plan yet. Please purchase a plan to continue using our services.",
    'basic_plan_limit_reached' => "Basic plan monthly invite limit reached",
    'event_member_limit_reached' => "The maximum member limit for this event has been reached.",
    'event_is_full' => "This event is full.",
    'event_content_liked' => 'You have liked this event.',
    'event_content_unliked' => 'You have removed your like from this event',

    // Add members Section in Host(Premium)
    'not_has_premium_plan' => 'You don\'t have a Premium Plan.',
    'members_added_successfully'=> 'Members added successfully.',
    'member_favorite_status_updated' => 'Member favorite status updated successfully.',
    'member_not_linked_with_host' => 'Member not linked with host.',
    'no_valid_members_found' => 'No valid members found.',
    'members_removed_successfully' => 'Members removed successfully.',
    'member_not_linked_with_host' => 'Member not linked with host.',
    'member_removed_successfully' => 'Member removed successfully.',

    // Notifications
    'notification_updated_successfully' => 'Notification updated successfully.',
    'notification_scheduled_successfully' => 'Notification scheduled successfully.',

    // verify Qr Code
    'invalid_qr_code' => 'Invalid QR Code',
    'unauthorized_qr_scan' => 'You are not authorized to scan this event QR code',
    'event_date_missing' =>'Event date is not set',
    'event_coming_soon' =>'Event is coming soon',
    'event_already_passed' =>'Event is already passed',
    'already_checked_in' => 'You have already checked in to this event',
    

    // Crud Fields
    'cruds' => [
        'event' => [
            'create_event'     => [
                'name'              => 'Event Name',
                'event_date_time'   => 'Date & Time',
                'member_limit'      => 'Maximum Member Number',
                'category_id'       => 'Event Category',
                'location'          => 'Event Location',
                'event_rules'       => 'Event Rules',
                'event_offers'      => 'Event Offers',
                'description'       => 'Event Description',
                'cover_image'       => 'Event Cover Photo',
            ],
            'invite_members'  => [
                'event_id'          => 'Event',
                'member_ids'        => 'Event Members'
            ],

            'event_pre_content_store'  => [
                'event_id'          => 'Event',
                'title'             => 'Title',
                'description'       => 'Description',
                'files'             => 'Upload Photo/Video',
            ],

            'event_review'  => [
                'event_id'          => 'Event',
                'review'             => 'Review',
                'files'             => 'Upload Photo/Video',
            ],
        ]
    ]
];