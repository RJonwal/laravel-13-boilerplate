<?php

namespace App\Domains\Api\Profile\Controllers;

use App\Domains\Api\Common\Requests\PasswordRequest;
use App\Http\Controllers\APIController;
use App\Domains\Api\Profile\Requests\UpdateRequest;
use App\Domains\Api\Profile\Requests\SendEmailOtpRequest;
use App\Domains\Api\Profile\Emails\SendEmailOtp;
use App\Domains\Api\Profile\Requests\VerifyEmailOtpRequest;
use App\Domains\Api\Profile\Requests\SendPhoneOtpRequest;
use App\Domains\Api\Profile\Requests\VerifyPhoneOtpRequest;
use App\Domains\Core\VenueType\Models\VenueType;
use App\Domains\Core\Setting\Models\Setting;
use App\Domains\Core\BlockUser\Models\BlockUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Twilio\Rest\Client;
use App\Domains\Core\User\Models\User;
use App\Domains\Core\Upload\Models\Uploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends APIController
{

    public function getProfile(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 401);
            }

            // Check if user has either host or member role
            $roleId = config('constant.roles.customer', 2);
            $hasRole = $user->roles->contains('id', $roleId);

            if (!$hasRole) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
            }

            $userData = [
                'name' => $user->name,
                'email' => $user->email,
                'country_code' => $user->country_code,
                'phone' => $user->phone,
                'profile_image_url' => $user->profileImage ? $user->profileImage->file_url : null, // Assuming profileImage relationship
            ];

            return $this->apiSuccess(["user" => $userData], trans('api.profile_retrieved'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.something_went_wrong'));
        }
    }

    public function updateProfile(UpdateRequest $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 401);
            }
            $roleId = config('constant.roles.customer', 2);
            $hasRole = $user->roles->contains('id', $roleId);

            if (!$hasRole) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
            }
            
            $updateData = $request->only(['name', 'country_code', 'phone']);

            // Update user profile
            $userUpdated = $user->update($updateData);

            if(!$userUpdated) {
                return $this->apiError(trans('api.profile_update_failed'), 'profile_update_failed');
            }

            if($request->has('profile_image')){
                $uploadId = null;
                $actionType = 'save';
                if($profileImageRecord = $user->profileImage){
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($user, $request->profile_image, 'user/profile-images',"user_profile", 'original', $actionType, $uploadId);
            }

            return $this->apiSuccess([], trans('api.profile_updated'));
        } catch (\Throwable $th) {
            // dd($th);
            return $this->apiError(trans('api.profile_update_failed'), 'profile_update_failed');
        }
    }

    // Email OTP Methods
    public function sendEmailOtp(SendEmailOtpRequest $request)
    {
        try {
            $user = Auth::user();
            $email = $request->validated()['email'];

            $activeUserExists = User::where('email', $email)
                ->whereNull('deleted_at')
                ->exists();

            if ($activeUserExists) {
                return $this->apiError(
                    trans('api.validation.email.unique'),
                    'email_already_taken',
                    [],
                    422
                );
            }

            // if (app()->environment('production')) {
                $otp = sprintf('%04d', mt_rand(0, 9999));
            // } else {
            //     $otp = 1234;
            // }

            // Store OTP in cache for specified minutes
            $cacheKey = "email_otp_{$user->id}_{$email}";
            $expireMinutes = config('otp.OTP_EXPIRE_MINUTES', 2);
            Cache::put($cacheKey, $otp, now()->addMinutes($expireMinutes));

            $this->sendEmailOtpp($email, $otp, $expireMinutes);

            return $this->apiSuccess([], trans('api.otp_send_email'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.profile_email_otp_send_fail'), 'profile_email_otp_send_fail', ['error' => $th->getMessage()], 500);
        }
    }

    public function verifyEmailOtp(VerifyEmailOtpRequest $request)
    {
        try {
            $user = Auth::user();
            $validatedData = $request->validated();
            $email = $validatedData['email'];
            $otp = $validatedData['otp'];

            $activeUserExists = User::where('email', $email)
                ->whereNull('deleted_at')
                ->exists();

            if ($activeUserExists) {
                return $this->apiError(
                    trans('api.validation.email.unique'),
                    'email_already_taken',
                    [],
                    422
                );
            }

            // Check OTP from cache
            $cacheKey = "email_otp_{$user->id}_{$email}";
            $storedOtp = Cache::get($cacheKey);

            if (!$storedOtp || strval($storedOtp) !== strval($otp)) {
                return $this->apiError(trans('api.invalid_expire_otp'), 'invalid_expire_otp', [], 400);
            }

            // Update user email
            $user->update(['email' => $email]);

            // Clear OTP from cache
            Cache::forget($cacheKey);

            return $this->apiSuccess([], trans('api.profile_email_updated'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.failed_otp_verify'), 'failed_otp_verify');
        }
    }

    // Phone OTP Methods
    public function sendPhoneOtp(SendPhoneOtpRequest $request)
    {
        try {
            $user = Auth::user();
            $phone = $request->validated()['contact_number'];

            $activeUserExists = User::where('contact_number', $phone)
                ->whereNull('deleted_at')
                ->exists();

            if ($activeUserExists) {
                return $this->apiError(
                    trans('api.validation.contact_number.unique'),
                    'contact_number_already_taken',
                    [],
                    422
                );
            }

            if (app()->environment('production')) {
                $otp = sprintf('%04d', mt_rand(0, 9999));
            } else {
                $otp = 1234;
            }

            // Store OTP in cache for specified minutes
            $cacheKey = "phone_otp_{$user->id}_{$phone}";
            $expireMinutes = config('otp.OTP_EXPIRE_MINUTES', 2);
            Cache::put($cacheKey, $otp, now()->addMinutes($expireMinutes));

            // Send OTP via Twilio
            // $this->sendTwilioOtp($phone, $otp);
            $data = ['otp' => $otp];

            return $this->apiSuccess($data, trans('api.otp_sent_to_phone'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.failed_otp_send'), 'failed_otp_send', ['error' => $th->getMessage()], 500);
        }
    }

    public function verifyPhoneOtp(VerifyPhoneOtpRequest $request)
    {
        try {
            $user = Auth::user();
            $validatedData = $request->validated();
            $phone = $validatedData['contact_number'];
            $otp = (string) $validatedData['otp']; //  Ensure OTP is treated as string

            // Check if another active user already has this phone
            $activeUserExists = User::where('contact_number', $phone)
                ->whereNull('deleted_at')
                ->exists();

            if ($activeUserExists) {
                return $this->apiError(
                    trans('api.validation.contact_number.unique'),
                    'contact_number_already_taken',
                    [],
                    422
                );
            }

            // Check OTP from cache
            $cacheKey = "phone_otp_{$user->id}_{$phone}";
            $storedOtp = (string) Cache::get($cacheKey); //  Always cast to string

            if (!$storedOtp || $storedOtp !== $otp) {
                return $this->apiError(
                    trans('api.invalid_expire_otp'),
                    'invalid_expire_otp',
                    [],
                    400
                );
            }

            //  OTP verified — update user phone
            $user->update(['contact_number' => $phone]);

            // Remove OTP from cache
            Cache::forget($cacheKey);

            $user->refresh();
            $user->load('roles');

            $profileData = $this->filterProfileData($user);

            return $this->apiSuccess($profileData, trans('api.profile_phone_updated'));
        } catch (\Throwable $th) {
            // Optional: remove dd() in production
            report($th);
            return $this->apiError(trans('api.failed_otp_verify'), 'failed_otp_verify');
        }
    }

    protected function sendEmailOtpp($email, $otp, $expireMinutes)
    {
        $subject = 'Email Verification OTP';

        Mail::to($email)->send(new SendEmailOtp($email, $otp, $subject, $expireMinutes));
    }

    /* protected function sendTwilioOtp($phone, $otp)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($phone, [
            'from' => config('services.twilio.from'),
            'body' => "Your OTP is: $otp. It will expire in " . (int) config('otp.OTP_EXPIRE_MINUTES', 2) . " minutes."
        ]);
    } */

    public function destroy(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error'   => trans('api.unauthenticated')
                ], 401);
            }

            DB::beginTransaction();

            // Delete profile image if exists
            if ($user->profile_image_url && $user->profileImage) {
                deleteFile($user->profileImage->id);
            }

            // Detach roles if any
            if (method_exists($user, 'roles')) {
                $user->roles()->sync([]);
            }

            // Soft delete or hard delete user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('api.account_deleted_successfully'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error'   => trans('api.something_went_wrong')
            ], 500);
        }
    }

    public function updatePassword(PasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->apiError(__('api.change_password.messages.update_password.validation.invalid_old_password'), 'invalid_old_password');                
            }
            $user->password = bcrypt($request->new_password);
            $user->save();
            DB::commit();
            return $this->apiSuccess([], __('api.change_password.messages.update_password.success_update'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiError(__('api.something_went_wrong'));
        }
    }
}
