<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller {

    public function index() {
        return view('categories.index');
    }

    public function store(CategoryRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();

        try {

            Category::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            DB::commit();

            Log::info('Succesfully created category');
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Log::info($e->getMessage());
        }

        return response()->json(['message' => 'Category has been created', 200]);
    }

    public function show($id) {
        
    }

    public function edit(Category $category) {
        return response()->json($category);
    }

    public function update(CategoryRequest $request, $category) {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            Category::where('id', $category)
                    ->update([
                        'name' => $data['name'],
                        'description' => $data['description']
            ]);

            DB::commit();

            Log::info('Succesfully updated category');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
        }

        return response()->json(['message' => 'Category has been updated', 200]);
    }

    public function delete(Category $category) {

        DB::beginTransaction();

        try {
            $category->delete();

            DB::commit();

            Log::info('Succesfully deleted category');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
        }

        return response()->json(['message' => 'Category has been deleted', 200]);
    }

}
