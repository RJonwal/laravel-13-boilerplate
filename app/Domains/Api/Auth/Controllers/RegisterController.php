<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Domains\Api\Auth\Requests\RegisterRequest;
use App\Http\Controllers\APIController;
use App\Domains\Core\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
                $userData['country_code'] = $request->country_code;
                $userData['phone'] = $request->phone;
            } else {
                $userData['social_user_id'] = $request->social_user_id;
            }

            $user = User::create($userData);

            // Assign Role        
            $user->roles()->sync([config('constant.roles.customer', 2)]);

            DB::commit();

            return $this->apiSuccess([], trans('messages.register_messages.success'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Registration error: ' , ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);

            return $this->apiError($e->getMessage());
        }
    }
}