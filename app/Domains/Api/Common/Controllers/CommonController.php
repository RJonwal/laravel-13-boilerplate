<?php

namespace App\Domains\Api\Common\Controllers;

use App\Domains\Core\City\Models\City;
use App\Domains\Core\Customer\Models\Customer;
use App\Domains\Core\Receipt\Models\Receipt;
use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use App\Scopes\ParentReceiptScope;


class CommonController extends APIController
{
    public function dashboard(Request $request)
    {
        $dashboardData = [];
        return $this->apiSuccess($dashboardData, 'Dashboard Data');
    }
}
