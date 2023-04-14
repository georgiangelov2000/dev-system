<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplierService
{

    private $supplier;
    private $storage_static_files = 'public/images/suppliers';

    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }


    public function getEditData()
    {
        $supplier = $this->supplier->load('categories', 'image');

        $result = [
            'categories' => $supplier->categories->pluck('id')->toArray(),
            'image' => $supplier->image
        ];
    
        return $result;
    }

    public function imageUploader($file)
    {
        $imageInfo = @getimagesize($file);

        if ($imageInfo && ($imageInfo[2] == IMAGETYPE_JPEG || $imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF)) {
            $hashedImage = Str::random(10) . '.' . $file->getClientOriginalExtension();

            $imageData = [
                'path' => config('app.url') . '/storage/images/suppliers/',
                'name' => $hashedImage,
            ];

            if (!Storage::exists($this->storage_static_files)) {
                Storage::makeDirectory($this->storage_static_files);
            }

            if ($imageData) {
                $image = new SupplierImage($imageData);
                $savedImage = $this->supplier->image()->save($image);
                if ($savedImage) {
                    $storedFile = Storage::putFileAs($this->storage_static_files, $file, $hashedImage);
                }
                return $image;
            } else {
                return false;
            }
        }
    }

    public function attachSupplierCategories($categories)
    {
        $supplier = Supplier::findOrFail($this->supplier->id);
        $supplier->categories()->sync($this->convertArrayValuesToNumeric($categories));
    }

    private function convertArrayValuesToNumeric($array): array
    {
        return array_map('intval', $array);
    }
}
