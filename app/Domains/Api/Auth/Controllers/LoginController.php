<?php

namespace App\Domains\Api\Auth\Controllers;

use App\Domains\Core\User\Resource\UserResource;
use App\Domains\Api\Auth\Requests\LoginRequest;
use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use App\Domains\Core\User\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends APIController
{
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
            return $this->apiError($th->getMessage());
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
            return $this->apiError(trans('api.logout.logout_failed'), 'logout_failed', ['error' => $th->getMessage()], 500);
        }
    }
}