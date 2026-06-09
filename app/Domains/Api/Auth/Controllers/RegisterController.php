<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Domains\Api\Auth\Emails\NewUserRegisteredMail;
use App\Domains\Api\Auth\Emails\WelcomeUserMail;
use App\Domains\Api\Auth\Requests\RegisterRequest;
use App\Http\Controllers\APIController;
use App\Domains\Core\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends APIController
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {        
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                
                'status' => 'active',
                'approval_status' => 1,
                'language' => $request->language ?? 'en',
                'login_type' => $request->register_type,
            ];

            if ($request->register_type == 'normal') {
                $userData['password'] = bcrypt($request->password);
                $userData['phone'] = $request->phone;
            } else {
                $userData['social_user_id'] = $request->social_user_id;
            }

            $user = User::create($userData);

            // Assign Role        
            $user->roles()->sync([config('constant.roles.customer', 2)]);

            DB::commit();

            Mail::to($user->email)->send(new WelcomeUserMail($user));   // new user: welcome mail
            
            Mail::to(getSetting('support_email'))->send(new NewUserRegisteredMail($user));   // super admin: new user register

            return $this->apiSuccess([], trans('messages.register_messages.success'));
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Registration error: ' , ['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]);

            return $this->apiError(trans('api.something_went_wrong'), 'something_went_wrong', ['error' => $th->getMessage()], 500); 
        }
    }
}