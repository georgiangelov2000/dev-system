<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;

class BrandController extends Controller {

    public function index() {
        return view('brands.index');
    }

    public function store(BrandRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();

        try {

            Brand::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            DB::commit();

            Log::info('Brand has been created');
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Log::info($e->getMessage());
        }

        return response()->json(['message' => 'Brand has been created', 200]);
    }

    public function show($id) {
        
    }

    public function edit(Brand $brand) {
        return response()->json($brand);
    }

    public function update(BrandRequest $request, $brand) {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            Brand::where('id', $brand)
                    ->update([
                        'name' => $data['name'],
                        'description' => $data['description']
            ]);

            DB::commit();

            Log::info('Brand has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
        }

        return response()->json(['message' => 'Brand has been updated', 200]);
    }

    public function delete(Brand $brand) {

        DB::beginTransaction();

        try {
            $brand->delete();

            DB::commit();

            Log::info('Brand has been deleted');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
        }

        return response()->json(['message' => 'Brand has been deleted', 200]);
    }

}
