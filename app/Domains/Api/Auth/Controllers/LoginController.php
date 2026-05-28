<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Domains\Core\User\Models\User;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends APIController
{
    // --- LOGIN METHOD 1: Request OTP for Mobile Login ---
    public function requestLoginOtp(Request $request , $resendOtp = false)
    {
        $request->validate(['phone' => ['required', 'regex:/^\+\d{10,15}$/']]);

        $phone = $request->phone;
        
        // Check if user exists with this phone number
        $user = User::where('contact_number', $phone)->first();

        if (!$user) {
            return $this->apiError(trans('api.user_not_found'), 'user_not_found', [], 404);
        }

        $userRoles = $user->roles->pluck('id')->toArray();
        $allowedRoles = [config('constant.roles.host', 3), config('constant.roles.member', 4)];

        $hasAccess = count(array_intersect($userRoles, $allowedRoles)) > 0;
        if (!$hasAccess) {
            return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
        }

        if(in_array(config('constant.roles.host'), $userRoles)){
            $userRole = 'host';
        } else if(in_array(config('constant.roles.member'), $userRoles)){
            $userRole = 'member';
        }

        // Check if user is approved
        if ($user->approval_status == 0 ) {
            return $this->apiError(trans('api.login.approval_pending'), 'approval_pending', ['user_role' => $userRole, 'name' => $user->name], 403);
        }

        if ($user->approval_status == 2 ) {
            return $this->apiError(trans('api.login.approval_rejected'), 'approval_rejected', ['user_role' => $userRole, 'name' => $user->name], 403);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return $this->apiError(trans('api.login.account_inactive'), 'account_inactive', ['user_role' => $userRole, 'name' => $user->name], 403);
        }

 
        // --- Set OTP based on environment ---
        if (app()->environment('production')) {
            $otp = rand(1000, 9999); 
        } else {
            $otp = 1234; 
        }

        $otpExpireMinutes = (int) config('otp.OTP_EXPIRE_MINUTES', 2);

        // Store OTP in cache
        Cache::put("login_otp:$phone", $otp, now()->addMinutes($otpExpireMinutes));
        Cache::put("login_otp_attempts:$phone", 0, now()->addMinutes($otpExpireMinutes));
        Cache::put("login_otp_last_sent:$phone", Carbon::now(), now()->addMinutes($otpExpireMinutes));

        try {
         
            if (app()->environment('production')) {
                $this->sendTwilioOtp($phone, $otp);
            }
                     
            $data = ['otp' => $otp];

            if($resendOtp) {
                return $this->apiSuccess($data, trans('api.otp_resend'));
            } else {
                return $this->apiSuccess($data, trans('api.otp_sent'));
            }
            
            
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.otp_failed'), 'otp_failed', [], 500);
        }
    }

    // --- LOGIN METHOD 1: Verify OTP and Login ---
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^\+\d{10,15}$/'],
            'otp' => ['required', 'string', 'digits:4'],
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get("login_otp:$phone");
        $attempts = Cache::get("login_otp_attempts:$phone", 0);

        if (!$cachedOtp) {
            return $this->apiError(trans('api.otp_expired'), 'otp_expired', [], 400);
        }

        if ($attempts >= (int) config('otp.OTP_MAX_ATTEMPTS', 5)) {
            return $this->apiError(trans('api.otp_max_attempts'), 'otp_max_attempts', [], 400);
        }

        if ($cachedOtp != $otp) {
            Cache::increment("login_otp_attempts:$phone");
            return $this->apiError(trans('api.otp_invalid'), 'otp_invalid', [], 400);
        }

        // Find user
        $user = User::where('contact_number', $phone)->first();
        if (!$user) {
            return $this->apiError(trans('api.user_not_found'), 'user_not_found', [], 404);
        }

        // Clean cache
        Cache::forget("login_otp:$phone");
        Cache::forget("login_otp_attempts:$phone");
        Cache::forget("login_otp_last_sent:$phone");

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load user relationships if needed
        $user->load('roles');

        // Prepare filtered user data
        $userData = $this->filterUserData($user, $token);

        $user->update(['is_first_login' => 1]);

        return $this->apiSuccess($userData, trans('api.login.login_successful'));
    }

    // --- LOGIN METHOD 1: Resend Login OTP ---
    public function resendLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^\+\d{10,15}$/'],
        ]);

        $phone = $request->phone;
        $lastSent = Cache::get("login_otp_last_sent:$phone");

        
        $cooldownSeconds = (int) config('otp.OTP_RESEND_COOLDOWN', 30);
        
        if ($lastSent && $lastSent->diffInSeconds(now()) < $cooldownSeconds) {
            $remainingSeconds = round($cooldownSeconds - $lastSent->diffInSeconds(now()));
            return $this->apiError(trans('api.otp_cooldown', ['seconds' => $remainingSeconds]), 'otp_cooldown', [], 429);
        }

        // Reuse requestLoginOtp method
        return $this->requestLoginOtp($request, true);
    }

    // --- LOGIN METHOD 2: Email/Password Login ---
    // public function login(Request $request)
    // {

    //     $request->validate([
    //         'email' => ['required', 'email'],
    //         'password' => ['required', 'string', 'min:6'],
    //     ]);

    //     $credentials = ['email' => $request->email, 'password' => $request->password];            
    //     if (!Auth::attempt($credentials)) {
    //         return $this->apiError(trans('messages.wrong_credentials'), 'wrong_credentials',[], 401);
    //     }

    //     $user = Auth::user();

    //     $userRoles = $user->roles->pluck('id')->toArray();
    //     $allowedRoles = [config('constant.roles.host', 3), config('constant.roles.member', 4)];

    //     $hasAccess = count(array_intersect($userRoles, $allowedRoles)) > 0;
    //     if (!$hasAccess) {
    //         return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
    //     }
        
    //     if(in_array(config('constant.roles.host'), $userRoles)){
    //         $userRole = 'host';
    //     } else if(in_array(config('constant.roles.member'), $userRoles)){
    //         $userRole = 'member';
    //     }

    //     // Check if user is approved
    //     if ($user->approval_status == 0 ) {
    //         return $this->apiError(trans('api.login.approval_pending'), 'approval_pending', ['user_role' => $userRole, 'name' => $user->name], 403);
    //     }

    //     if ($user->approval_status == 2 ) {
    //         return $this->apiError(trans('api.login.approval_rejected'), 'approval_rejected', ['user_role' => $userRole, 'name' => $user->name], 403);
    //     }

    //     // Check if user is active
    //     if ($user->status !== 'active') {
    //         return $this->apiError(trans('api.login.account_inactive'), 'account_inactive', ['user_role' => $userRole, 'name' => $user->name], 403);
    //     }

    //     // Create token
    //     $token = $user->createToken('auth-token')->plainTextToken;

    //     // Load user relationships if needed
    //     $user->load('roles');

    //     // Prepare filtered user data
    //     $userData = $this->filterUserData($user, $token);

    //     $user->update([
    //         'is_first_login' => 1,
    //         'device_token'   => $request->header('Device-Token')
    //     ]);

    //     return $this->apiSuccess($userData, trans('api.login.login_successful'));
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        //  Find user ignoring global scopes (so even pending/rejected can attempt)
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->apiError(trans('messages.wrong_credentials'), 'wrong_credentials', [], 401);
        }

        // Check role access
        $userRoles = $user->roles->pluck('id')->toArray();
        $allowedRoles = [config('constant.roles.customer', 2)];

        $hasAccess = count(array_intersect($userRoles, $allowedRoles)) > 0;
        if (!$hasAccess) {
            return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
        }

        // Determine user role
        if (in_array(config('constant.roles.host'), $userRoles)) {
            $userRole = 'host';
        } elseif (in_array(config('constant.roles.member'), $userRoles)) {
            $userRole = 'member';
        } else {
            $userRole = 'unknown';
        }

        // Approval checks
        if ($user->approval_status == 0) {
            return $this->apiError(trans('api.login.approval_pending'), 'approval_pending', [
                'user_role' => $userRole,
                'name' => $user->name
            ], 403);
        }

        if ($user->approval_status == 2) {
            return $this->apiError(trans('api.login.approval_rejected'), 'approval_rejected', [
                'user_role' => $userRole,
                'name' => $user->name
            ], 403);
        }

        // Active status check
        if ($user->status !== 'active') {
            return $this->apiError(trans('api.login.account_inactive'), 'account_inactive', [
                'user_role' => $userRole,
                'name' => $user->name
            ], 403);
        }

        //  Login manually (since we didn’t use Auth::attempt)
        Auth::login($user);

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load relationships if needed
        $user->load('roles');

        // Filter and prepare response data
        $userData = $this->filterUserData($user, $token);

        // Update first login + device token
        $user->update([
            'is_first_login' => 1,
            'device_token'   => $request->header('Device-Token'),
        ]);

        return $this->apiSuccess($userData, trans('api.login.login_successful'));
    }


    // --- LOGOUT ---
    public function logout(Request $request)
    {
        try {
            // Delete current access token
            $request->user()->currentAccessToken()->delete();

            return $this->apiSuccess([], trans('api.logout.logout_successful'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.logout.logout_failed'), 'logout_failed', ['error' => $th->getMessage()], 500);
        }
    }

    protected function filterUserData($user, $token)
    {
        $data = [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'contact_number' => $user->contact_number,
            'instagram_handle' => $user->instagram_handle,
            'profile_image' => $user->is_member ? $user->featuredProfileImage?->file_url ?? null : $user->profileImage['file_url'] ?? null,
            'tiktok' => $user->tiktok,
            'venue_name' => $user->venue_name,
            'venue_type' => $user->venue_type,
            'member_tag' => $user->member_tag ?? null,
            'is_paused' => $user->is_paused === 1 ? 'paused' : 'not_paused',
            'venue_description' => $user->venue_description,
            'status' => $user->status,
            'approval_status' => $user->approval_status,
            'roles' => $user->roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name]),
            'token' => $token,
            'token_type' => 'Bearer',
            'is_first_login' => $user->is_first_login,
            'plan_type' => $user->has_active_subscription && $user->active_plan ? $user->active_plan->plan_type : '',
            'active_subscription' => !empty($user->active_plan) ? true : false,
            'unread_notifications_count' => $user->unreadNotifications ? $user->unreadNotifications->count() : 0,
            'location_id' => $user->event_location_id,
        ];

        // If user has the "host" role → add company_name
        if ($user->roles->contains('id', config('constant.roles.host'))) {
            $data['company_name'] = $user->company_name;
        }

        return $data;
    }


    // --- HELPER: Send OTP via Twilio ---
    protected function sendTwilioOtp($phone, $otp)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($phone, [
            'from' => config('services.twilio.from'),
            'body' => "Your login OTP is: $otp. It will expire in " . (int) config('otp.OTP_EXPIRE_MINUTES', 2) . " minutes."
        ]);
    }
}