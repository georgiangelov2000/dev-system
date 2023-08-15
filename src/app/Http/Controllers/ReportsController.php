<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(){
        return view('reports.index');
    }

    public function takeReport(Request $request){
        $request->only([
            'type_export' => 'integer|required',
            'export' =>  'integer|required',
            'data_type_export' => 'integer|required',
            'month' => 'required'
        ]);
    }


}
