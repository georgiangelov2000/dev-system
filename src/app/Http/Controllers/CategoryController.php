<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use App\Http\Requests\CategoryRequest;
use App\Helpers\LoadStaticData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    private $staticDataHelper;

    private $dir = 'public/images/categories';

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
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $file = isset($data['image']) ? $data['image'] : false;
            $imagePath = Storage::url($this->dir);

            $category = new Category();
            $category->name = $data['name'];
            $category->description = $data['description'];

            if ($file) {
                $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);
                $category->image_path = $imagePath . '/' . $hashed_image;
            }

            if (isset($data['sub_categories']) && count($data['sub_categories'])) {
                foreach ($data['sub_categories'] as $subCategoryId) {
                    $subCategory = SubCategory::findOrFail($subCategoryId);
                    $subCategory->category_id = $category->id;
                    $subCategory->save();
                }
            }

            $category->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
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
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $file = isset($data['image']) ? $data['image'] : false;

            $imagePath = Storage::url($this->dir);

            $category->name = $data['name'];
            $category->description = $data['description'];

            if($file) {
                $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                if($category->image_path) {
                    $storedFile = str_replace('/storage', '', $category->image_path);
                    if (Storage::disk('public')->exists($storedFile)) {
                        Storage::disk('public')->delete($storedFile);
                    }
                }
                Storage::putFileAs($this->dir, $file, $hashed_image);
                $category->image_path = $imagePath .'/'. $hashed_image;
            }

            if (isset($data['sub_categories']) && count($data['sub_categories'])) {
                foreach ($data['sub_categories'] as $subCategoryId) {
                    $subCategory = SubCategory::findOrFail($subCategoryId);
                    $subCategory->category_id = $category->id;
                    $subCategory->save();
                }
            }

            $category->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Category has not been updated'], 500);
        }

        return response()->json(['message' => 'Category has been updated'], 200);
    }

    public function deleteImage(Category $category)
    {
        DB::beginTransaction();

        try {
            // Remove the leading /storage from the image_path
            $imagePath = str_replace('/storage', '', $category->image_path);
            
            // Check if the image path exists in storage
            if (Storage::disk('public')->exists($imagePath)) {
                // If it exists, delete the image
                Storage::disk('public')->delete($imagePath);

                // Update the image_path column in your database
                $category->image_path = null;
                $category->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Image has not been deleted'], 500);
        }

        return response()->json(['message' => 'Image has been deleted'], 200);
    }

    public function delete(Category $category)
    {
        DB::beginTransaction();

        try {
            $imagePath = str_replace('/storage', '', $category->image_path);

            // Check if the image path exists and delete it
            if ($category->image_path && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $category->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Category has not been deleted'], 500);
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
            return response()->json(['message' => 'Sub category has not been detached'], 500);
        }

        return response()->json(['message' => 'Sub category has been detached'], 200);
    }
}
