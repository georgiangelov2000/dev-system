<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(){
        return view('logs.index');
    }

    public function systemLogs(){
        return view('logs.system_logs');
    }
}
