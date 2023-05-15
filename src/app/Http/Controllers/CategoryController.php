<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CategoryRequest;
use App\Helpers\LoadStaticData;

class CategoryController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('categories.index', [
            'subcategories' => $this->staticDataHelper->callSubCategories()
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {

            $category = Category::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            foreach ($data['sub_categories'] as $subCategoryId) {
                $subCategory = SubCategory::findOrFail($subCategoryId);
                $subCategory->category_id = $category->id;
                $subCategory->save();
            }

            DB::commit();
            Log::info('Category has been created');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error' => 'Category has not been created'], 500);
        }

        return response()->json(['message' => 'Category has been created'], 200);
    }

    public function edit(Category $category)
    {

        $allSubCategories = $this->staticDataHelper->callSubCategories();
        $relatedSubCategories = $category->subCategories()->pluck('id')->toArray();

        return response()->json([
            "category" => $category, 
            'relatedSubCategory' => $relatedSubCategories,
            "allSubCategories" => $allSubCategories
        ]);

    }

    public function update(Category $category, CategoryRequest $request)
    {

        $data = $request->validated();
        DB::beginTransaction();

        try {
            $category->update([
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            foreach ($data['sub_categories'] as $subCategoryId) {
                $subCategory = SubCategory::findOrFail($subCategoryId);
                $subCategory->category_id = $category->id;
                $subCategory->save();
            }

            DB::commit();
            Log::info('Category has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error' => 'Failed to update category'], 500);
        }

        return response()->json(['message' => 'Category has been updated'], 200);
    }

    public function delete(Category $category)
    {
        DB::beginTransaction();

        try {
            $category->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => 'Failed to delete category'], 500);
        }

        return response()->json(['message' => 'Category has been deleted'], 200);
    }

    public function detachSubCategory($id)
    {

        DB::beginTransaction();

        try {
            $subCategory = SubCategory::find($id);
            $subCategory->category_id = null;
            $subCategory->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => 'Sub category has not been detached'], 500);
        }

        return response()->json(['message' => 'Sub category has been detached'], 200);
    }
}
