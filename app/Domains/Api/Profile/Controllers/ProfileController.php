<?php

namespace App\Domains\Api\Profile\Controllers;

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
use Illuminate\Support\Facades\Storage;

class ProfileController extends APIController
{

    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 401);
            }

            // Check if user has either host or member role
            $hostRoleId = config('constant.roles.host', 3);
            $memberRoleId = config('constant.roles.member', 4);

            $hasHostRole = $user->roles->contains('id', $hostRoleId);
            $hasMemberRole = $user->roles->contains('id', $memberRoleId);

            if (!$hasHostRole && !$hasMemberRole) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
            }

            $user->load('roles');

            $profileData = $this->filterProfileData($user);

            return $this->apiSuccess($profileData, trans('api.profile_retrieved'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.something_went_wrong'));
        }
    }

    public function getProfilebyHostMember(Request $request, $user_uuid)
    {
        try {

            $user = Auth::user();

            
            $hostRoleId = config('constant.roles.host', 3);
            $memberRoleId = config('constant.roles.member', 4);

            $hasHostRole = $user->roles->contains('id', $hostRoleId);
            $hasMemberRole = $user->roles->contains('id', $memberRoleId);

            if (!$hasHostRole && !$hasMemberRole) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
            }

            $user = User::where('uuid', $user_uuid)->select([ 'id', 'uuid', 'name', 'event_location_id','instagram_handle',
        'tiktok', 'status', 'approval_status', 'company_name', 'venue_name', 'venue_description', 'about_description'])->first();

            if (!$user) {
                return $this->apiError(trans('api.user_not_found'), 'not_found', [], 404);
            }

            $user->load('roles');

            $profileData = $this->filterProfileData($user);

            unset($profileData['email'], $profileData['contact_number']);

            return $this->apiSuccess($profileData, trans('api.profile_retrieved'));

        } catch (\Throwable $th) {
            return $this->apiError(trans('api.something_went_wrong'));
        }
    }


    // public function updateProfile(UpdateRequest $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         if (!$user) {
    //             return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 401);
    //         }
    //         $hostRoleId = config('constant.roles.host', 3);
    //         $memberRoleId = config('constant.roles.member', 4);

    //         $hasHostRole = $user->roles->contains('id', $hostRoleId);
    //         $hasMemberRole = $user->roles->contains('id', $memberRoleId);

    //         if (!$hasHostRole && !$hasMemberRole) {
    //             return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
    //         }
    //         $validatedData = $request->validated();
    //         $updateData = [
    //             'name' => $validatedData['name'],
    //             'instagram_handle' => $validatedData['instagram_handle'],
    //             'tiktok' => $validatedData['tiktok'] ?? $user->tiktok,
    //         ];



    //         // Add venue fields only if user is a host
    //         if ($hasHostRole) {
    //             $updateData['company_name'] = $validatedData['company_name'];
    //             $updateData['venue_name'] = $validatedData['venue_name'];
    //             $updateData['venue_description'] = $validatedData['venue_description'];

    //             $venueType = VenueType::where('uuid', $validatedData['venue_type'])->first();
    //             $updateData['venue_type'] = $venueType->id;
    //         } else {
    //             $updateData['about_description'] = $request->about_description;
    //         }

    //         // Update user profile
    //         $user->update($updateData);
    //         $authUser = $user;

    //         if ($request->has('profile_image')) {
    //             $uploadId = null;
    //             $actionType = 'save';
    //             if ($profileImageRecord = $authUser->profileImage) {
    //                 $uploadId = $profileImageRecord->id;
    //                 $actionType = 'update';
    //             }
    //             uploadImage($authUser, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
    //         }

    //         $user->refresh();
    //         $user->load('roles');

    //         $profileData = $this->filterProfileData($user);

    //         return $this->apiSuccess($profileData, trans('api.profile_updated'));
    //     } catch (\Throwable $th) {
    //         return $this->apiError(trans('api.profile_update_failed'), 'profile_update_failed');
    //     }
    // }

    public function updateProfile(UpdateRequest $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 401);
            }
            $hostRoleId = config('constant.roles.host', 3);
            $memberRoleId = config('constant.roles.member', 4);

            $hasHostRole = $user->roles->contains('id', $hostRoleId);
            $hasMemberRole = $user->roles->contains('id', $memberRoleId);

            if (!$hasHostRole && !$hasMemberRole) {
                return $this->apiError(trans('api.unauthorized'), 'unauthorized', [], 403);
            }
            $validatedData = $request->validated();
            $updateData = [
                'name' => $validatedData['name'],
                'instagram_handle' => normalize_social_handle($validatedData['instagram_handle']),
                'tiktok' => normalize_social_handle($validatedData['tiktok'] ?? $user->tiktok),
            ];

            // Add venue fields only if user is a host
            if ($hasHostRole) {
                $updateData['company_name'] = $validatedData['company_name'];
                $updateData['venue_name'] = $validatedData['venue_name'];
                $updateData['venue_description'] = $validatedData['venue_description'];

                $venueType = VenueType::where('uuid', $validatedData['venue_type'])->first();
                $updateData['venue_type'] = $venueType->id;
            } else {
                $updateData['about_description'] = $request->about_description;
                $updateData['event_location_id'] = $validatedData['event_location_id'];
            }

            // Update user profile
            $user->update($updateData);
            $authUser = $user;

            // if ($request->has('profile_image')) {
            //     $uploadId = null;
            //     $actionType = 'save';
            //     if ($profileImageRecord = $authUser->profileImage) {
            //         $uploadId = $profileImageRecord->id;
            //         $actionType = 'update';
            //     }
            //     uploadImage($authUser, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
            // }

            //   Handle Host single image
            if ($hasHostRole && $request->has('profile_image')) {
                $uploadId = null;
                $actionType = 'save';
                if ($profileImageRecord = $authUser->profileImage) {
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($authUser, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
            }

            //  Handle Member multiple images
        //    if ($hasMemberRole && $request->has('profile_images')) {
        //         foreach ($request->file('profile_images') as $image) {
        //             uploadImage($user, $image, 'user/profile-images', 'user_profile');
        //         }
        //     }

        // 🔹 MEMBER → multiple images (REPLACE) with one featured
            // if ($hasMemberRole && $request->hasFile('profile_images')) {

            //     DB::transaction(function () use ($request, $user) {

            //         // 1️⃣ DELETE OLD IMAGES (DB + STORAGE)
            //         $oldImages = $user->profileImages;

            //         foreach ($oldImages as $oldImage) {
            //             Storage::disk('public')->delete($oldImage->file_path);
            //             $oldImage->delete();
            //         }

            //         // 2️⃣ INSERT NEW IMAGES
            //         $featuredIndex = (int) $request->input('featured_index', 0);

            //         foreach ($request->file('profile_images') as $index => $image) {

            //             uploadImage(
            //                 $user,
            //                 $image,
            //                 'user/profile-images',
            //                 'user_profile',
            //                 'original',
            //                 'save',
            //                 null,
            //                 null,
            //                 $index === $featuredIndex ? 1 : 0
            //             );
            //         }
            //     });
            // }

            // if ($hasMemberRole) {

            //     DB::transaction(function () use ($request, $user) {

            //         // 🔴 DELETE ONLY SELECTED IMAGES
            //         if ($request->filled('deleted_image_ids')) {

            //             $imagesToDelete = $user->profileImages()
            //                 ->whereIn('id', $request->deleted_image_ids)
            //                 ->get();

            //             foreach ($imagesToDelete as $image) {
            //                 Storage::disk('public')->delete($image->file_path);
            //                 $image->delete();
            //             }
            //         }

            //         // 🔵 ADD NEW IMAGES
            //         if ($request->hasFile('profile_images')) {

            //             $featuredIndex = (int) $request->input('featured_index', 0);

            //             foreach ($request->file('profile_images') as $index => $image) {
            //                 uploadImage(
            //                     $user,
            //                     $image,
            //                     'user/profile-images',
            //                     'user_profile',
            //                     'original',
            //                     'save',
            //                     null,
            //                     null,
            //                     $index === $featuredIndex ? 1 : 0
            //                 );
            //             }
            //         }
            //     });
            // }

            if ($hasMemberRole) {

                DB::transaction(function () use ($request, $user) {

                    //  DELETE SELECTED IMAGES
                    if ($request->filled('deleted_image_ids')) {

                        $imagesToDelete = $user->profileImages()
                            ->whereIn('id', $request->deleted_image_ids)
                            ->get();

                        foreach ($imagesToDelete as $image) {
                            Storage::disk('public')->delete($image->file_path);
                            $image->delete();
                        }
                    }

                    //  UPLOAD NEW IMAGES (NO FEATURED HERE)
                    if ($request->hasFile('profile_images')) {
                        foreach ($request->file('profile_images') as $image) {
                            uploadImage(
                                $user,
                                $image,
                                'user/profile-images',
                                'user_profile'
                            );
                        }
                    }


                    // SET FEATURED IMAGE BY FINAL INDEX
                    if ($request->has('featured_index')) {

                        // RESET ALL FEATURED FLAGS
                        $user->profileImages()->update(['is_featured' => 0]);

                        $featuredIndex = (int) $request->featured_index;

                        // IMPORTANT: reset keys using ->values()
                        $finalImages = $user->profileImages()
                            ->orderBy('created_at') // OR id, but be consistent with UI
                            ->get(); //  THIS IS REQUIRED

                        if ($finalImages->has($featuredIndex)) {

                            uploads::where('id', $finalImages[$featuredIndex]['id'])->update(['is_featured' => 1]);
                        }
                    }

                });
            }

            $user->refresh();
            $user->load('roles');

            $profileData = $this->filterProfileData($user);

            return $this->apiSuccess($profileData, trans('api.profile_updated'));
        } catch (\Throwable $th) {
            // dd($th);
            return $this->apiError(trans('api.profile_update_failed'), 'profile_update_failed');
        }
    }


    protected function filterProfileData($user)
    {
        $hostRoleId   = config('constant.roles.host', 3); 
        $memberRoleId = config('constant.roles.member', 4);
    
        $hasHostRole   = $user->roles->contains('id', $hostRoleId);
        $hasMemberRole = $user->roles->contains('id', $memberRoleId);

        $profileData = [
        'uuid' => $user->uuid,
        'name' => $user->name,
        'email' => $user->email,
        'contact_number' => $user->contact_number,
        'instagram_handle' => $user->instagram_handle,
        'tiktok' => $user->tiktok,
        'status' => $user->status,
        'approval_status' => $user->approval_status,
        'roles' => $user->roles->first()?->name,
        'instagram_details' => trans('messages.try_again'),
        ];

        if ($hasMemberRole) {

            // $profileData['profile_image'] = $user->featuredProfileImage?->file_url;

            $profileData['profile_images'] = $user->profileImages
                ->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->file_url,
                    'is_featured' => (bool) $img->is_featured,
                ])
                ->values();

        } else {
            // HOST → single image only
            $profileData['profile_image'] = $user->profileImage?->file_url;
        }

        if ($hasHostRole) {
            $profileData['company_name'] = $user->company_name;
            $profileData['venue_name'] = $user->venue_name;
            $profileData['venue_type'] = $user->venueType?->uuid;
            $profileData['venue_description'] = $user->venue_description;
        } else {
            $profileData['about_description'] = $user->about_description;
            $profileData['city'] = $user->eventLocation?->name ?? null;
            $profileData['city_id'] = $user->event_location_id ?? null;
        }

 
        // ADD Instagram details if approved and handle exists
        // if($user->approval_status == 1 && $user->instagram_handle){
         
        //     $url = env('INSTA_SCRAP_URL') . $user->instagram_handle;
        //     $token = env('INSTA_SCRAP_TOKEN');

        //     $insta_profile = getInstaProfile($url, $token);
        //     if (!empty($insta_profile['error'])) {
        //         $profileData['instagram_details'] = trans('messages.try_again');
        //         return $profileData;
        //     }
        //     $profileData['instagram_details'] = $insta_profile['status'] == 'success' ? $insta_profile : trans('messages.try_again');
        // }

        return $profileData;
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

            $user->refresh();
            $user->load('roles');
            $profileData = $this->filterProfileData($user);

            return $this->apiSuccess($profileData, trans('profile_email_updated'));
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

    // public function verifyPhoneOtp(VerifyPhoneOtpRequest $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         $validatedData = $request->validated();
    //         $phone = $validatedData['contact_number'];
    //         $otp = $validatedData['otp'];



    //           $activeUserExists = User::where('contact_number', $phone)
    //             ->whereNull('deleted_at')
    //             ->exists();

    //         if ($activeUserExists) {
    //             return $this->apiError(
    //                 trans('api.validation.contact_number.unique'),
    //                 'contact_number_already_taken',
    //                 [],
    //                 422
    //             );
    //         }

    //         // Check OTP from cache
    //         $cacheKey = "phone_otp_{$user->id}_{$phone}";
    //         $storedOtp = Cache::get($cacheKey);

    //         dd($storedOtp, $otp);

    //         if (!$storedOtp || $storedOtp !== $otp) {
    //             return $this->apiError(trans('api.invalid_expire_otp'), 'invalid_expire_otp',[], 400);
    //         }

    //         // Update user phone
    //         $user->update(['contact_number' => $phone]);

    //         // Clear OTP from cache
    //         Cache::forget($cacheKey);

    //         $user->refresh();
    //         $user->load('roles');
    //         $profileData = $this->filterProfileData($user);

    //         return $this->apiSuccess($profileData, trans('api.profile_phone_updated'));
    //     } catch (\Throwable $th) {
    //         dd($th);
    //         return $this->apiError(trans('api.failed_otp_verify'), 'failed_otp_verify');
    //     }
    // }

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


    // Helper Methods
    // protected function sendEmailOtpp($email, $otp, $expireMinutes)
    // {
    //     // You can customize this email template as needed
    //     Mail::raw("Your OTP is: {$otp}. It will expire in {$expireMinutes} minutes.", function ($message) use ($email) {
    //         $message->to($email)
    //             ->subject('Email Verification OTP');
    //     });
    // }
    protected function sendEmailOtpp($email, $otp, $expireMinutes)
    {
        $subject = 'Email Verification OTP';

        Mail::to($email)->send(
            new SendEmailOtp($email, $otp, $subject, $expireMinutes)
        );
    }

    protected function sendTwilioOtp($phone, $otp)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($phone, [
            'from' => config('services.twilio.from'),
            'body' => "Your OTP is: $otp. It will expire in " . (int) config('otp.OTP_EXPIRE_MINUTES', 2) . " minutes."
        ]);
    }

    public function blockList()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->apiError(trans('api.unauthenticated'), 'unauthorized', [], 401);
            }

            // Only include blocks where the blocked user is NOT soft-deleted
            $blockedUsers = BlockUser::where('blocked_by', $user->id)
                ->whereHas('blockedUser', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with(['blockedUser' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->with('profileImage'); //  Added: eager load profile image
                }])
                ->get()
                ->map(function ($block) {
                    $blocked = $block->blockedUser;

                    return [
                        'uuid' => $blocked?->uuid,
                        'name' => $blocked?->name,
                        'email' => $blocked?->email,
                        'contact_number' => $blocked?->contact_number,
                        // 'profile_image' => $blocked?->profileImage?->file_url, //  Added: show user’s profile image
                        'profile_image' => $blocked->is_member ? $blocked->featuredProfileImage?->file_url ?? null : $blocked->profileImage['file_url'] ?? null,
                        'reason' => $block->reason,
                        'blocked_at' => optional($block->blocked_at)
                            ?->format(config('constant.frontend_date_format.date_time')),
                        'member_tag' => $blocked?->member_tag
                    ];
                });

            return $this->apiSuccess(['blocked_users' => $blockedUsers]);
        } catch (\Throwable $th) {
            return $this->apiError(trans('api.something_went_wrong'));
        }
    }

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

    public function helpAndSupport()
    {
        try {

            $settings = Setting::whereIn('key', ['support_email', 'support_contact'])
                ->select('key', 'value')
                ->get();

            return $this->apiSuccess(['help_and_support' => $settings]);
        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'error'   => trans('api.something_went_wrong')
            ], 500);
        }
    }

    public function togglePauseAccount(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Toggle the pause status
            $user->is_paused = !$user->is_paused;
            $user->save();

            return $this->apiSuccess([], $user->is_paused ? trans('api.account_paused') : trans('api.account_unpaused'));
        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'error'   => trans('api.something_went_wrong')
            ], 500);
        }
    }
}
