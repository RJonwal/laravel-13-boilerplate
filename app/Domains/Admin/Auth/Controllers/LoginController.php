<?php

namespace App\Domains\Admin\Auth\Controllers;

use App\Domains\Admin\Auth\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(){
        return view('Auth::login');
    }

    public function submitLogin(LoginRequest $request){
        try {        
            $remember_me = !is_null($request->remember_me) ? true : false;

            $credentialsOnly = $request->only('email', 'password');
            if (Auth::attempt($credentialsOnly, $remember_me))
            {
                $user = Auth::user();         
                // restrict to do login to client admin when no active subscription and no trial subscription
                if($user->status != 'active'){
                    Auth::guard('web')->logout();
                    return response()->json([
                        'success' => false,
                        'message' => trans('messages.account_deactivate')
                    ], 400);
                }

                // Prevent unauthorized roles (hosts/members)
                if ($user->is_customer) {
                    Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => __('auth.unauthorize'),
                    ], 400);
                }

                // Flash messages after login
                session()->flash('success', trans('messages.login_success'));

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('admin.dashboard')
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.wrong_credentials')
            ], 400);
        }        
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return response()->json([
                'success' => true,
                'redirect_url' => route('login'),
        ]);
    }
}
