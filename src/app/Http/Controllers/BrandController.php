<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Brand;
use App\Http\Requests\BrandRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    private $dir = 'public/images/brands';

    public function index(): View
    {
        return view('brands.index');
    }

    public function store(BrandRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $file = isset($data['image']) ? $data['image'] : false;
            $imagePath = Storage::url($this->dir);
            
            $brand = new Brand();
            $brand->name = $data['name'];
            $brand->description = $data['description'];

            if($file) {
                $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);
                $brand->image_path = $imagePath .'/'. $hashed_image;
            }

            $brand->save();
            DB::commit();
            return response()->json(['message' => 'Brand has been created'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Brand has not been created'], 500);
        }
    }

    public function edit(Brand $brand): JsonResponse
    {
        return response()->json($brand);
    }

    public function update(BrandRequest $request, Brand $brand): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $file = isset($data['image']) ? $data['image'] : false;
            
            $imagePath = Storage::url($this->dir);

            $brand->name = $data['name'];
            $brand->description = $data['description'];

            if($file) {
                $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                if($brand->image_path) {
                    $storedFile = str_replace('/storage', '', $brand->image_path);
                    if (Storage::disk('public')->exists($storedFile)) {
                        Storage::disk('public')->delete($storedFile);
                    }
                }
                Storage::putFileAs($this->dir, $file, $hashed_image);
                $brand->image_path = $imagePath .'/'. $hashed_image;
            }

            $brand->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json(['error' => 'Brand has not been updated'], 500);
        }

        return response()->json(['message' => 'Brand has been updated'], 200);
    }

    public function deleteImage(Brand $brand): JsonResponse
    {
        DB::beginTransaction();
    
        try {
            // Remove the leading /storage from the image_path
            $imagePath = str_replace('/storage', '', $brand->image_path);
    
            // Check if the image path exists in storage
            if (Storage::disk('public')->exists($imagePath)) {
                // If it exists, delete the image
                Storage::disk('public')->delete($imagePath);
    
                // Update the image_path column in your database
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
    

    public function delete(Brand $brand): JsonResponse
    {
        DB::beginTransaction();
    
        try {
            $imagePath = str_replace('/storage', '', $brand->image_path);
    
            // Check if the image path exists and delete it
            if ($brand->image_path && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
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
    
}
