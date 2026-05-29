<?php

namespace App\Domains\Api\Dashboard\Controllers;

use App\Http\Controllers\APIController;

class DashboardController extends APIController
{
    public function dashboard()
    {
        $authUser = request()->user('sanctum');
        $data = [];


        return $this->apiSuccess($data, 'Dashboard Data');
    }    
}
