<?php

namespace App\Domains\Admin\Dashboard\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        try {

            return view('Dashboard::index');
        } catch (\Throwable $th) {
            abort(500);
        }
    }
}
