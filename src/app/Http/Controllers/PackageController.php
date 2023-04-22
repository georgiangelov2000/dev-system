<?php

namespace App\Http\Controllers;
use App\Http\Requests\PackageRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    
    public function create(){
        return view('packages.create');   
    }

    public function store(PackageRequest $request){
        $data = $request->validated();

        DB::beginTransaction();

        try {
            
            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Failed to create package');
        }
        
    }
}
