<?php

return [

    'crud'=>[
        'add_record'    => 'Successfully Added !',
        'update_record' => 'Successfully Updated !',
        'delete_record' => 'This record has been succesfully deleted!',
        'restore_record'=> 'This record has been succesfully Restored!',
        'merge_record'  => 'This record has been succesfully Merged!',
        'approve_record'=> 'Record Successfully Approved !',
        'status_update' => 'Status successfully updated!',
        'record_not_found' => 'Record not found',

        // subscription package delete but package is taken by user
        'subscription_plan_taken' => 'Subscription Plan cannot be deleted because it is already taken by users.',

        'customer_has_receipts' => 'Unable to delete: receipts exist for this customer.',
        'customer_receipts_city_updated' => 'City has been updated in all receipts of this customer.',
        'city_has_customers' => 'This city cannot be deleted because customers are associated with it.',
        'city_has_receipts'  => 'This city cannot be deleted because receipts are associated with it.',


        // remove profile image:
        'profile' => [
            'update_image' => 'Profile image updated Successfully',
            'remove_image' => 'Profile image removed Successfully',
            'profile_image_not_found' => 'Profile image not found',
            'onceClickedRecordDeleted' => 'Do you want to remove profile image ?',
            'portfolio_upload_success' => 'Portfolio image uploaded successfully',
            'update_request_sent' => 'Profile update request sent for approval',
            'already_update_sent' => 'You already have a pending profile update request',
            'profile_image_not_uploaded' => "Profile is not uploaded"
        ]
    ],

    'account_deactivate' => 'Your account has been deactivated.',
    'access_denied'      => 'Access denied. You are not allowed to login here.',
    'permission_denied' => 'Access denied.',
    'permission_denied_error' => 'Access denied. You do not have permission to perform this action.',
    'account_ban'      => 'Your account has been banned. Please contact admin',
    // 'register_success_with_otp_verification' => "Register successfully from "
    'logout_confirmation' => 'Are you sure you want to logout?',

    'unable_to_add_blank_field' => 'Sorry, Unable to add a blank field in',
    'data_already_exists' => 'Sorry, You cannot create new with the same name so use existing.',
    'socail_user_not_found' => 'Socail User Account not found. Please register',

    'areYouSure'=>'Are you sure you want to delete this record?',
    'areYouSureapprove'=>'Are you sure you want to Approve this record?',
    'areYouSurerestore'=>'Are you sure you want to Restore this Database? It will delete your current database.',
    'deletetitle'=>'Delete Confirmation',
    'restoretitle'=>'Restore Confirmation',
    'approvaltitle'=>'Approval Confirmation',
    'areYouSureRestore'=>'Are you sure you want to restore this record?',
    'error_message'   => 'Something went wrong....please try again later!',
    'has_equipment_error' => 'Cannot delete this equipment category because it has associated equipment.',
    'has_user_error' => 'Cannot delete this role because it has associated users.',
    'no_record_found' => 'No Records Found!',
    'suspened'=> "Your account has been suspened!",
    'invalid_email'=>'Invalid Email',
    'invalid_otp'=>'Invalid OTP',
    'invalid_pin'=>'Invalid PIN',
    'wrong_credentials'=>'These credentials do not match our records!',
    'not_activate'=>'Your account is not activated.',
    'otp_sent_email'=>'We have successfully sent OTP on your Registered Email',
    'expire_otp'=> 'OTP has been Expired',
    'verified_otp'=> 'OTP successfully Verified.',
    'invalid_token_email'=> 'Invalid Token or Email!',
    'success'=>'Success',
    'register_success'=>'Your account created successfully! Please wait for the approval!',
    'register_user'=>'Your account created successfully!',
    'login_success'=>'You have logged in successfully!',
    'logout_success'=>'Logged out successfully!',
    'warning_select_record'=> 'Please select at least one record',
    'required_role'=> "User with the specified email doesn't have the required role.",
    'combined_success' => 'Receipts Successfully Combined.',
    'parent_receipt_not_found' => 'Parent receipt not found.',
    'restore_success' => 'Combined receipt restored successfully.',
    'merge_warning' => 'This will combine all pending entries. This action cannot be undone!',
    'only_one_receipt_exists' => 'Only one receipt exists — nothing to combine. please Refresh and try again.',

    
    'invalid_token'                 => 'Your access token has been expired. Please login again.',
    'not_authorized'                => 'Not Authorized to access this resource',
    'not_found'                     => 'Not Found!',
    'endpoint_not_found'            => 'Endpoint not found',
    'resource_not_found'            => 'Resource not found',
    'token_invalid'                 => 'Token is invalid',
    'unexpected'                    => 'Unexpected Exception. Try later',
    
    'data_retrieved_successfully'   => 'Data retrieved successfully',
    'record_retrieved_successfully' => 'Record retrieved successfully',
    'record_created_successfully'   => 'Record created successfully',
    'record_updated_successfully'   => 'Record updated successfully',
    'record_deleted_successfully'   => 'Record deleted successfully',
    'password_updated_successfully' => 'Password updated successfully',

    'profile_updated_successfully'  => 'Profile updated successfully',
    'account_deactivate'            => 'Your account has been deactivated. Please contact the admin.',
    'user_account_deactivate'      => 'Your account has been deactivated.',

    'contact' => [
        'store' => [
            'success' => "Your message has been sent successfully. We will get back to you as soon as possible"
        ]
        ],
    'rating' => [
        'store' => [
            'success' => "Your feedback has been submitted successfully. We appreciate your time and effort in helping us improve our service."
        ]
        ],

    'notification'=>[
        'not_found' => 'Notification not found',
        'mark_as_read' => 'Notification marked as read',
        'no_notification'=>'No notifications to clear!',
        'clear_notification' => 'All notifications have been cleared',
        'delete'             => 'Notification has been deleted successfully!',
    ],

    'warning_messages' => [
        'milestone_not_selected' => "Please select Milestone"
    ],

    'register_messages' => [
        'send_otp' => "Otp send to verify phone number. Otp will expire after 5 minutes",
        'otp_expired' => "Otp Has been expired",
        'otp_invalid' => "Invalid OTP",
        'success' => "User registered successfully",
        'otp_verified' => "otp verified successfully",
        'otp_verified' => "otp verified successfully",
        'otp_verified' => "otp verified successfully",
        'otp_verified' => "otp verified successfully",
    ],
    'account_approval'      => 'your account is under approval. Please continue to browse our website.',
    'account_rejected'      => 'your account is rejected.',
    'register_success_email_verify' => "Registration successful. Please check your email for verification link",

    'email_verify' => [
        'parameter_missing' => "Their are some parameter is missing",
        'link_expire' => "Email verification link has been expired",
        'invalid_link' => "Email verification link invalid",
        'already_verified' => "Email already verified",
        'email_verified' => "Email verified successfully",
        'resend_link' => "Verification link has been resent to your email address"
    ],
    'equipment_not_found' => 'Equipment not found',
    'partner_not_found' => 'Partner not found',
    'enquiry_success' => 'Your inquiry has been sent successfully',
    'subscribe_success' => 'You have subscribed successfully.',
    'email_exists' => 'This email is already subscribed.',

    'logout' => [
        'success' => 'Logged out successfully',
        'error' => 'Failed to logout, please try again.'
    ],

    'old_password_incorrect' => 'Old Password is incorrect',
    'review_success' => 'Thank you! Your review has been submitted and is pending approval.',
    'review_updated_successfully' => 'Review Updated Successfully',
    'review_deleted_successfully' => 'Review Deleted Successfully',

    'pending_review_message' => 'Your update request has been submitted and is pending admin review. You will be notified once it is approved or rejected.',
    'cannot_edit_pending_review_event' => 'Cannot edit event while it is pending review.',

    'email_not_verified' => 'Your email address is not verified. Please verify your email to continue.',
    'email_not_verified_sent_link' => 'Your email address is not verified. A new verification link has been sent to your email.',

    'become_partner_request_success' => 'Your request to become partner has been submitted successfully. Please wait for the approval',
    'become_partner_already_request_sent' => 'You already have a pending become partner request',

    'review_required' => "The review field is required when rating is less than 3.",
    'reject_popup_status_updated' => "Reject popup status updated successfully",
    'partner_denied' => "Your account is linked to our partner system. Please log in through the Partner Portal to continue.",

    'already_reviewed_partner' => "You've already submitted a review for this partner",
    'already_reviewed_website' => "You've already submitted a review for this platform",

    'media_type_required' => 'The social media type is required',

    'customer_not_found' => 'Select customer not found',
    'customer_city_update_error' => "Customer city cannot be updated because receipts already exist for this customer in the current city.",

    'ip_restricted' => "Your IP address is not allowed to access this portal.",

    'api' => [
        'login' => [
            'required_device_id'                => 'Device ID is missing from request headers',
            'device_not_register_with_staff'    => 'This device is not registered for your account. Please contact the administrator for support.',
            'device_not_register'               => 'This device is not registered. Please contact the administrator for support.',
            'login_with_credentials_success'    => 'Credentials verified. Please provide your PIN to continue.',
            'invalid_pin'                       => 'The PIN you entered is incorrect.',
        ],
        'customer' => [
            'create-success' => "customer created successfully",
            'update-success' => "customer updated successfully",
            'delete-success' => "customer deleted successfully",
        ],

        'receipt' => [
            'create-success' => "Receipt created successfully",
            'update-success' => "Receipt updated successfully",
            'delete-success' => "customer deleted successfully",
        ],

        'city' => [
            'create-success' => "City created successfully",
            'update-success' => "City updated successfully",
            'delete-success' => "City deleted successfully",
        ],
    ],

    'swal' => [
        'invalid_amount_title' => 'Invalid Amount!',
        'invalid_amount_text' => 'Amount must be greater than 0.',

        'amount_exceed_title' => 'Amount Exceeds Limit!',
        'amount_exceed_text' => 'The entered amount exceeds the customer limit. Are you sure you want to continue?',

        'confirm_continue' => 'Yes, continue',
        'cancel_clear' => 'No, clear amount',
        
    ],



];
