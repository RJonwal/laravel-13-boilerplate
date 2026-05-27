<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Domains\Admin\User\Resource\UserResource;
use App\Domains\Api\Auth\Requests\LoginRequest;
use App\Domains\Core\Device\Models\Device;
use App\Domains\Core\User\Models\User;
use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends APIController
{
    public function login(LoginRequest $request){
        try {
            $deviceId = $request->header('Device-Id'); // get from mobile header
            if(!$deviceId) {
                return $this->apiError(trans('messages.api.login.required_device_id'),'required_device_id',[], 422);
            }

            $user_login = $request->user_login;
            $loginType = filter_var($user_login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            $credentials = [$loginType => $user_login, 'password' => $request->password];            
            if (!Auth::attempt($credentials)) {
                return $this->apiError(trans('messages.wrong_credentials'),'wrong_credentials',[], 401);
            }
            $loginUser = Auth::user();
        
            if($loginUser->status != 'active'){
                return $this->apiError(trans('messages.account_deactivate'),'account_deactivate',[], 422);
            }

            // check device exists for this user
            $device = Device::where('device_id', $deviceId)->where('staff_id', $loginUser->id)->first();
            if (!$device) {
                return $this->apiError(trans('messages.api.login.device_not_register_with_staff'),'device_not_register_with_staff',[], 422);
            }

            $allowedIps = getSetting('allowed_ips');
            if (!$loginUser->is_super_admin && !in_array($request->ip(), $allowedIps)) {
                // $loginUser->currentAccessToken()->delete();

                return response()->json([
                    'success' => false,
                    'error_type' => 'ip_restricted',
                    'message' => trans('messages.ip_restricted'),
                    'errors' => []
                ], 400);
            }
    
            return $this->apiSuccess(['require_pin' => true], trans('messages.api.login.login_with_credentials_success'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();
            return $this->apiSuccess([], trans('messages.logout_success'));
        } catch (\Exception $e) {
            return $this->apiError(trans('messages.logout_fail'));
        }
    }
}
