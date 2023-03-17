<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\CategorySubCategory;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Helpers\LoadStaticData;

class CategoryController extends Controller {

    public function index() {
        return view('categories.index', [
            'subcategories' => LoadStaticData::loadSubcategories()['unAssignedSubcategories']
        ]);
    }

    public function store(CategoryRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();

        try {

            $category = Category::create([
                        'name' => $data['name'],
                        'description' => $data['description']
            ]);

            if ($category && isset($data['subcategory']) && count($data['subcategory'])) {
                foreach ($data['subcategory'] as $subcategoryId) {
                    CategorySubCategory::create([
                        'category_id' => $category->id,
                        'sub_category_id' => $subcategoryId
                    ]);
                }
            }

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

        $allSubCategories = LoadStaticData::loadSubcategories()['unAssignedSubcategories'];
        
        return response()->json(["category" => $category, "allSubCategories" => $allSubCategories]);
    }

    public function update(CategoryRequest $request, $category) {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $category = Category::findOrFail($category);

            $category->update([
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            if (isset($data['subcategory']) && count($data['subcategory'])) {
                $category->subCategories()->syncWithoutDetaching($data['subcategory']);
            }

            DB::commit();

            Log::info('Successfully updated category');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to update category'], 500);
        }

        return response()->json(['message' => 'Category has been updated'], 200);
    }

    public function delete(Category $category) {
        DB::beginTransaction();

        try {
            if ($category->subCategories) {
                $category->subCategories()->detach();
            }

            $category->delete();

            DB::commit();

            Log::info('Successfully deleted category');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Failed to delete category'], 500);
        }

        return response()->json(['message' => 'Category has been deleted'], 200);
    }

    public function detachSubCategory($id) {
        DB::beginTransaction();

        try {
            $related_subcategory = CategorySubCategory::findOrFail($id);
            $related_subcategory->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Failed to detach sub category'], 500);
        }
        return response()->json(['message' => 'Sub category has been detached'], 200);
    }

}
