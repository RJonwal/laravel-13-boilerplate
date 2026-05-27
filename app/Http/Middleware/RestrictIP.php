<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $allowedIps = getSetting('allowed_ips');
        // $authUser = Auth::user();
        // // dd($authUser->name, $request->ip());

        // if (!$authUser->is_super_admin && !in_array($request->ip(), $allowedIps)) {
        //     if ($this->isApiRequest($request)) {
        //         $authUser->currentAccessToken()->delete();

        //         return response()->json([
        //             'success' => false,
        //             'error_type' => 'Unauthorized',
        //             'message' => "Unauthorized",
        //             'errors' => []
        //         ], 401);
        //     } else {                
        //         Auth::guard('web')->logout();
        //         return redirect()->route('login')->with('error', trans('messages.ip_restricted'));
        //     }
        // }
        return $next($request);
    }

    private function isApiRequest(Request $request)
    {
        return strpos($request->path(), 'api/') === 0
            || $request->is('api/*')
            || $request->header('Accept') === 'application/json';
    }
}
