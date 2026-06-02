<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Domains\Core\User\Resource\UserResource;
use App\Domains\Api\Auth\Requests\LoginRequest;
use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use App\Domains\Core\User\Models\User;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends APIController
{
    public function requestLoginOtp(Request $request , TwilioService $twilioService, $resendOtp = false)
    {
        $request->validate([
            'type' => ['required', 'in:phone,email'],
            'value' => ['required', 'string'],
        ]);
        
        $type = $request->type;
        $value = trim($request->value);

        $user = User::query()
        ->when($type === 'phone', fn ($q) => $q->where('phone', $value))
        ->when($type === 'email', fn ($q) => $q->where('email', $value))
        ->first();

        if (!$user) {
            return $this->apiError(trans('api.user_not_found'), 'user_not_found', [], 404);
        }

        $userRoles = $user->roles->pluck('id')->toArray();
        $allowedRoles = [config('constant.roles.customer', 2)];

        $hasAccess = count(array_intersect($userRoles, $allowedRoles)) > 0;
        if (!$hasAccess) {
            return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
        }

        // Check if user is approved
        if ($user->approval_status == 0 ) {
            return $this->apiError(trans('api.login.approval_pending'), 'approval_pending', [ 'name' => $user->name], 403);
        }

        if ($user->approval_status == 2 ) {
            return $this->apiError(trans('api.login.approval_rejected'), 'approval_rejected', ['name' => $user->name], 403);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return $this->apiError(trans('api.login.account_inactive'), 'account_inactive', ['name' => $user->name], 403);
        }

 
        // --- Set OTP based on environment ---
        if (app()->environment('production')) {
            $otp = rand(100000, 999999); 
        } else {
            $otp = 123456; // Fixed OTP for non-production environments
        }

        $otpExpireMinutes = (int) config('otp.OTP_EXPIRE_MINUTES', 2);

        
        // Store OTP in cache
        $cachePrefix = "login_otp:{$type}:{$value}";
        Cache::put($cachePrefix, $otp, now()->addMinutes($otpExpireMinutes));
        Cache::put("login_otp_attempts:{$type}:{$value}", 0, now()->addMinutes($otpExpireMinutes));
        Cache::put("login_otp_last_sent:{$type}:{$value}", now(), now()->addMinutes($otpExpireMinutes));
        Cache::put("login_otp_last_sent:{$type}:{$value}", now()->timestamp, now()->addMinutes($otpExpireMinutes));

        try {
            if (app()->environment('production')) {
                if ($type === 'phone') {
                    $twilioService->sendSms($value, "Your login OTP is: {$otp}");
                } else {
                    Mail::raw("Your login OTP is: {$otp}", function ($message) use ($value) {
                            $message->to($value)->subject('Login OTP');
                        }
                    );
                }
            }

            return $this->apiSuccess([], $resendOtp ? trans('api.otp_resend') : trans('api.otp_sent'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.otp_failed'), 'otp_failed', [], 500);
        }
    }

    // --- LOGIN METHOD 1: Verify OTP and Login ---
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:phone,email'],
            'value' => ['required'],
            'otp' => ['required', 'digits:6'],
        ]);

        try {
            $type = $request->type;
            $value = $request->value;
            $otp = $request->otp;

            $cacheKey = "login_otp:{$type}:{$value}";

            $cachedOtp = Cache::get($cacheKey);

            $attempts = Cache::get("login_otp_attempts:{$type}:{$value}", 0);

            if (!$cachedOtp) {
                return $this->apiError(trans('api.otp_expired'), 'otp_expired', [], 400);
            }

            if ($attempts >= (int) config('otp.OTP_MAX_ATTEMPTS', 5)) {
                return $this->apiError(trans('api.otp_max_attempts'), 'otp_max_attempts', [], 400);
            }

            if ($cachedOtp != $otp) {
                Cache::increment("login_otp_attempts:{$type}:{$value}");
                return $this->apiError(trans('api.otp_invalid'), 'otp_invalid', [], 400);
            }

            // Find user
            $user = User::query()
            ->when($type === 'phone', fn ($q) => $q->where('phone', $value))
            ->when($type === 'email', fn ($q) => $q->where('email', $value))
            ->first();
            if (!$user) {
                return $this->apiError(trans('api.user_not_found'), 'user_not_found', [], 404);
            }

            // Clean cache
            Cache::forget("login_otp:{$type}:{$value}");
            Cache::forget("login_otp_attempts:{$type}:{$value}");
            Cache::forget("login_otp_last_sent:{$type}:{$value}");

            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Load user relationships if needed
            $user->load('roles');

            // Prepare filtered user data
            $userResource = new UserResource($user);

            return $this->apiSuccess(['access_token' => $token, 'user' => $userResource], trans('messages.login_successful'));
        } catch (\Throwable $th) {
            return $this->apiError($th->getMessage(), $th->getCode(), [], 400);
        }
    }

    // --- LOGIN METHOD 1: Resend Login OTP ---
    public function resendLoginOtp(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:phone,email'],
            'value' => ['required'],
        ]);

        try {
            $type = $request->type;
            $value = $request->value;

            // Check if user exists
            $user = User::query()
                ->when($type === 'phone', fn ($q) => $q->where('phone', $value))
                ->when($type === 'email', fn ($q) => $q->where('email', $value))
                ->first();

            if (!$user) {
                return $this->apiError(trans('api.user_not_found'), 'user_not_found', [], 404);
            }
            $type = $request->type;
            $value = $request->value;
            $lastSent = Cache::get("login_otp_last_sent:{$type}:{$value}");

            
            $cooldownSeconds = (int) config('otp.OTP_RESEND_COOLDOWN', 30);
            
            if ($lastSent && $lastSent->diffInSeconds(now()) < $cooldownSeconds) {
                $remainingSeconds = round($cooldownSeconds - $lastSent->diffInSeconds(now()));
                return $this->apiError(trans('api.otp_cooldown', ['seconds' => $remainingSeconds]), 'otp_cooldown', [], 429);
            }

            // Reuse requestLoginOtp method
            return $this->requestLoginOtp($request, app(TwilioService::class), true);
        } catch (\Throwable $th) {
             Log::error('Resend Login OTP error: ' , ['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]);
            return $this->apiError($th->getMessage(), $th->getCode(), [], 400);
        }
    }

    public function login(LoginRequest $request){
        try {
            $user_login = $request->user_login;
            $loginType = filter_var($user_login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            if(in_array($request->login_type, ['google','apple', 'facebook'])){
                $loginUser = User::where('login_type', $request->login_type)->where('social_user_id', $request->social_user_id)->first();
                if(!$loginUser){
                    return $this->apiError(trans('messages.socail_user_not_found'),'socail_user_not_found',[], 422);
                }
            } else {
                $credentials = [$loginType => $user_login, 'password' => $request->password];            
                if (!Auth::attempt($credentials)) {
                    return $this->apiError(trans('messages.wrong_credentials'),'wrong_credentials',[], 401);
                }
                $loginUser = Auth::user();
            }

            $role_id = $loginUser->roles->first()->id;

            // Check user is login role
            if ($role_id != config('constant.roles.customer')) {
                return $this->apiError(trans('messages.access_denied'),'access_denied',[], 403);
            }
 

            // Approval checks
            if ($loginUser->approval_status == 0) {
                return $this->apiError(trans('api.login.approval_pending'), 'approval_pending', [], 403);
            }

            if ($loginUser->approval_status == 2) {
                return $this->apiError(trans('api.login.approval_rejected'), 'approval_rejected', [], 403);
            }

            // Active status check
            if ($loginUser->status !== 'active') {
                return $this->apiError(trans('api.login.account_inactive'), 'account_inactive', [], 403);
            }

            $user = User::find($loginUser->id);

            $user->tokens()->delete();

            $user->update([
                'fcm_token'  => $request->header('Device-Token')
            ]);

            $token = $loginUser->createToken('mobile-token')->plainTextToken;
    
            $userResource = new UserResource($user);
            return $this->apiSuccess(['access_token' => $token, 'user' => $userResource], trans('messages.login_success'));
        } catch (\Throwable $th) {
            Log::error('Login error: ' , ['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]);
            return $this->apiError(trans('api.something_went_wrong'), 'something_went_wrong', ['error' => $th->getMessage()], 500);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        try {
            // Delete current access token
            $request->user()->currentAccessToken()->delete();

            return $this->apiSuccess([], trans('api.logout.logout_successful'));
        } catch (\Throwable $th) {
            Log::error('Logout error: ' , ['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]);
            return $this->apiError(trans('api.logout.logout_failed'), 'logout_failed', ['error' => $th->getMessage()], 500);
        }
    }
}