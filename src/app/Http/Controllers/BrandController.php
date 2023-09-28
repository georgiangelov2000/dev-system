<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Brand;
use App\Http\Requests\BrandRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BrandController extends Controller
{
    private $dir = 'public/images/brands';
    private $helper;

    public function __construct(FunctionsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Display the brand index view.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('brands.index');
    }

    /**
     * Store a new brand in the database.
     *
     * @param BrandRequest $request The validated brand request data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the store operation.
     */

    public function store(BrandRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->brandProcessing($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Brand has not been created'], 500);
        }
        return response()->json(['message' => 'Brand has been created'], 200);
    }

    /**
     * Retrieve and return data for editing a brand.
     *
     * @param Brand $brand The brand to be edited.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the brand data for editing.
     */

    public function edit(Brand $brand): JsonResponse
    {
        return response()->json($brand);
    }

    /**
     * Update an existing brand in the database.
     *
     * @param Brand $brand The brand to be updated.
     * @param BrandRequest $request The validated brand request data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the update operation.
     */

    public function update(Brand $brand, BrandRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->brandProcessing($data, $brand);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Brand has not been updated'], 500);
        }

        return response()->json(['message' => 'Brand has been updated'], 200);
    }

    /**
     * Delete the image associated with a brand and update the brand's image path.
     *
     * @param Brand $brand The brand whose image is to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the image deletion operation.
     */

    public function deleteImage(Brand $brand): JsonResponse
    {
        DB::beginTransaction();

        try {
            $isItFileDeleted = $this->helper->deleteImage($brand);

            if ($isItFileDeleted) {
                $brand->image_path = null;
                $brand->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Image has not been deleted'], 500);
        }

        return response()->json(['message' => 'Image has been deleted'], 200);
    }

    /**
     * Delete a brand from the database, including its associated image if present.
     *
     * @param Brand $brand The brand to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the brand deletion operation.
     */
    public function delete(Brand $brand): JsonResponse
    {
        DB::beginTransaction();

        try {
            if ($brand->image_path) {
                $this->helper->deleteImage($brand);
            }
            $brand->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Brand has not been deleted'], 500);
        }

        return response()->json(['message' => 'Brand has been deleted'], 200);
    }


    /**
     * Process and save brand data to the database.
     *
     * @param array $data The brand data to be processed.
     * @param Brand|null $brand The brand to be updated (optional).
     */

    private function brandProcessing(array $data, $brand = null)
    {
        $brand = $brand ? $brand : new Brand();

        $brand->name = $data['name'];
        $brand->description = $brand['description'];

        if (isset($data['image'])) {
            $this->helper->imageUploader($data['image'], $brand, $this->dir);
        }

        $brand->save();
    }
}
