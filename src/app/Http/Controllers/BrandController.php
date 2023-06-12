<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BrandRequest;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    public function index()
    {
        return view('brands.index');
    }

    public function store(BrandRequest $request)
    {

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
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error' => 'Category has not been created'], 500);
        }

        return response()->json(['message' => 'Brand has been created', 200]);
    }

    public function show($id)
    {
    }

    public function edit(Brand $brand)
    {
        return response()->json($brand);
    }

    public function update(BrandRequest $request, $brand)
    {
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

    public function delete(Brand $brand)
    {

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

    public function purchases(Brand $brand)
    {
        return view('brands.purchases', compact('brand'));
    }

    public function detachPurchase(Brand $brand, Request $request)
    {
        $purchase_id = $request->purchase_id;
        
        $validatedData = $request->validate([
            'purchase_id' => 'integer|exists:purchases,id',
        ]);
    
        $purchase_id = $validatedData['purchase_id'];
    
        try {
            DB::beginTransaction();
    
            $product = Product::find($purchase_id);
    
            if (!$product) {
                throw new \Exception('Product not found');
            }
    
            $brand->purchases()->detach($purchase_id);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Purchase has been detached successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'message' => 'An error occurred while detaching the purchase',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
