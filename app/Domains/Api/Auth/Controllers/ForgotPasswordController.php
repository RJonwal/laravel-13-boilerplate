<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Domains\Core\User\Models\User;
use App\Http\Controllers\APIController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends APIController
{
    // forget password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email,deleted_at,NULL']]);

        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            
            // --- Set Token based on environment ---
            if (app()->environment('production')) {
                $token = rand(1000, 9999); 
            } else {
                $token = 1234;
            }

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::table('password_reset_tokens')
                ->updateOrInsert(
                    ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()],
                    ['email' => $request->email]
                );

            $expiretime = config('auth.passwords.users.otp_expire') . ' Minutes';
            $user->sendPasswordResetOtpNotification($user, $token, $expiretime);

            DB::commit();

            return $this->apiSuccess([],trans('auth.messages.forgot_password.otp_sent'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Forgot password error: ' , ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
            return $this->apiError(trans('api.something_went_wrong'), 'something_went_wrong', ['error' => $e->getMessage()], 500);
        }
    }

    // Verify forget password OTP
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:password_reset_tokens,email',
                'otp'   => 'required|string|min:4'
            ]);


            $passwordReset = DB::table('password_reset_tokens')
                ->where('token', $request->otp)
                ->where('email', $request->email)
                ->latest()
                ->first();

            if (!$passwordReset) {
                return $this->apiError(trans('auth.messages.forgot_password.validation.invalid_otp'), 'invalid_otp');
            }

            if (Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.otp_expire'))->isPast()) {
                return $this->apiError(trans('auth.messages.forgot_password.validation.expire_otp'), 'expire_otp');
            }

            return $this->apiSuccess(['token' => encrypt($request->otp)], trans('auth.messages.forgot_password.validation.verified_otp'));
        } catch (\Throwable $e) {
            Log::error('Verify OTP error: ' , ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
            return $this->apiError(trans('api.something_went_wrong'), 'something_went_wrong', ['error' => $e->getMessage()], 500);
        }
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'     => ['required'],
            'email'     => ['required', 'email', 'exists:users,email,deleted_at,NULL'],
            'password'  => ['required', 'string', 'min:8', 'regex:/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*\d)(?=.*[@$!%*#?&]).+$/u'],
            'confirmed_password' => ['required', 'string', 'same:password']
        ], [
            'password.regex' => 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);
        DB::beginTransaction();
        try {
            $token = decrypt($request->token);
            $passwordReset = DB::table('password_reset_tokens')->where('token', $token)
                ->where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$passwordReset) {
                return $this->apiError(trans('auth.messages.forgot_password.validation.invalid_token_email'), 'invalid_token_email');
            }

            $user = User::where('email', $passwordReset->email)->first();
            if (!$user) {
                return $this->apiError(trans('auth.messages.forgot_password.validation.email_not_found'), 'email_not_found');
            }

            $user->password = bcrypt($request->password);
            $user->save();
            DB::table('password_reset_tokens')->where('email', $passwordReset->email)->delete();

            DB::commit();

            return $this->apiSuccess([],trans('auth.messages.forgot_password.success_update'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Reset password error: ' , ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
            return $this->apiError(trans('api.something_went_wrong'), 'something_went_wrong', ['error' => $e->getMessage()], 500);
        }
    }
}
