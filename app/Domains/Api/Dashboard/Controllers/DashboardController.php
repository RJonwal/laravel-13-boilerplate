<?php

namespace App\Domains\Api\Dashboard\Controllers;

use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Log;

class DashboardController extends APIController
{
    public function dashboard()
    {
        try {
            $authUser = request()->user('sanctum');
            $data = [];


            return $this->apiSuccess($data, 'Dashboard Data');
        } catch (\Throwable $th) {
            Log::error('Dashboard error: ' , ['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]);
            
            return $this->apiError(trans('api.something_went_wrong'), 'something_went_wrong', ['error' => $th->getMessage()], 500); 
        }

    }    
}
