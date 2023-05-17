<?php

namespace App\Http\Controllers;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function index(){
        // dd($this->dashboard());

        return view('dashboard.home',[
            'dashboard_data' => $this->dashboard()
        ]);
    }
    public function dashboard()
    {
        $dashboardService = new DashboardService();
        $data = $dashboardService->getData();
        return $data;
    }
}
