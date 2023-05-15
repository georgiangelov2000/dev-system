<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubCategoryController extends Controller
{
    public function index(){
        return view('subcategories.index');
    }

    public function store(SubCategoryRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
    
        try {
            $subcategory = SubCategory::create([
                'name' => $data['name'],
            ]);
    
            DB::commit();
            Log::info('Category has been created');
            
            return response()->json(['message' => 'Category has been created'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error' => 'Category has not been created'], 500);
        }
    }

    public function edit(SubCategory $subcategory) {
        return response()->json($subcategory);
    }

    public function update(SubCategory $subcategory, SubCategoryRequest $request){
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $subcategory->update([
                'name' => $data['name']
            ]);
            DB::commit();
            Log::info('Subcategory has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => 'Category has not been updated'], 500);
        }
        return response()->json(['message' => 'Category has been updated'], 200);
    }

    public function delete(SubCategory $subcategory) {
        DB::beginTransaction();

        try {
            $subcategory->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => 'Category has not been deleted'], 500);
        }

        return response()->json(['message' => 'Category has been deleted'], 200);
    }

}
