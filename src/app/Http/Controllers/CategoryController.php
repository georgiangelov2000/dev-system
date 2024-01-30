<?php

namespace App\Http\Controllers;

use id;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Log as LogModel;
use App\Helpers\LoadStaticData;
use App\Helpers\FunctionsHelper;
use App\Helpers\RedisCacheHelper;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CategoryRequest;
use App\Models\RedisModel;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private $staticDataHelper;
    private $dir = 'public/images/categories';
    private $helper;
    private $categoryService;
    private $redisModel;
    private $redisCacheHelper;

    public function __construct(
        LoadStaticData $staticDataHelper,
        FunctionsHelper $helper,
        CategoryService $categoryService,
        RedisModel $redisModel,
        RedisCacheHelper $redisCacheHelper
    ) {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
        $this->categoryService = $categoryService;
        $this->redisModel = $redisModel;
        $this->redisCacheHelper = $redisCacheHelper;
    }

    /**
     * Display the categories index view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('categories.index', [
            'subcategories' => $this->staticDataHelper->callSubCategories()
        ]);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $record = $this->categoryProcessing(null, $data);
            
            if(!$record) {
                throw new \Exception("Category has not been created");
            }

            $log = $this->helper->logData(
                'store_category',
                'store_category_action',
                $record->name,
                Auth::user(),
                now(),
            );

            LogModel::create($log);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Category has not been created'], 500);
        }

        return response()->json(['message' => 'Category has been created'], 200);
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Update an existing Category with the provided data.
     *
     * @param Category $category The Category model to update.
     * @param CategoryRequest $request The validated request data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Category $category, CategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            // Get the validated data from the request.
            $data = $request->validated();
            
            // Call the categoryProcessing method to update the Category model.
            $record = $this->categoryProcessing($category, $data);

            if(!$record) {
                throw new \Exception("Category has not been updated");
            }

            $log = $this->helper->logData(
                'update_category',
                'update_category_action',
                $record->name,
                Auth::user(),
                now(),
            );

            LogModel::create($log);

            // Commit the database transaction.
            DB::commit();
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction and log the error.
            DB::rollback();
            Log::error($e->getMessage());

            // Return a JSON response indicating an error occurred.
            return response()->json(['error' => 'Category has not been updated'], 500);
        }

        // Return a JSON response indicating that the Category has been successfully updated.
        return response()->json(['message' => 'Category has been updated'], 200);
    }

    /**
     * Delete the image associated with a Category and update its image_path attribute.
     *
     * @param Category $category The Category model to delete the image from.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage(Category $category)
    {
        DB::beginTransaction();

        try {
            // Attempt to delete the image file and store the result.
            $isItFileDeleted = $this->helper->deleteImage($category);

            // If the image file was deleted, update the image_path attribute to null.
            if ($isItFileDeleted) {
                $category->image_path = null;
                $category->save();
            }

            // Commit the database transaction.
            DB::commit();
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction and log the error.
            DB::rollback();
            Log::error($e->getMessage());

            // Return a JSON response indicating an error occurred.
            return response()->json(['error' => 'Image has not been deleted'], 500);
        }

        // Return a JSON response indicating that the image has been successfully deleted.
        return response()->json(['message' => 'Image has been deleted'], 200);
    }

    /**
     * Delete the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Category $category)
    {
        DB::beginTransaction();

        try {

            // Check if the category exists before proceeding with deletion
            if (!$category->exists) {
                throw new \Exception("Category not found");
            }

            // Check if the category has been assigned to products
            if ($category->products->isNotEmpty()) {
                throw new \Exception("Category has been assigned to purchases");
            }

            // If the category has an image, delete it
            if ($category->image_path) {
                $this->helper->deleteImage($category);
            }

            $name = $category->name;

            // Delete the category
            $category->delete();

            // Log the deletion action
            $log = $this->helper->logData(
                'delete_category',
                'delete_category_action',
                $name,
                Auth::user(),
                now(),
            );

            LogModel::create($log);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Category has been deleted'], 200);
    }

    /**
     * Detach the specified sub-category from a category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Process and save a Category model along with its associated data.
     *
     * @param Category|null $category The Category model to update, or null to create a new one.
     * @param array $data The data for the Category.
     * @return void
     */
    private function categoryProcessing($category = null, array $data)
    {
        // Create a new Category model if $category is null, otherwise update the existing one.
        $category = $category ? $category : new Category;

        // Set the Category's name and description.
        $category->name = $data['name'];
        $category->description = $data['description'];

        // If an image file is provided, upload it and update the image_path attribute.
        if (isset($data['image'])) {
            $this->helper->imageUploader($data['image'], $category, $this->dir,'image_path');
        }

        // Save the Category model to the database.
        $category->save();

        // Attach the selected subcategories to the Category.
        if(!empty($data['sub_categories'])) {
            $this->categoryService->attachSubCategories($data['sub_categories'], (int) $category->id);
        }
        
        return $category;
    }
}
