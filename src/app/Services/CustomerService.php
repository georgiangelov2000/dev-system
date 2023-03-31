<?php

namespace App\Services;
use App\Models\Customer;
use App\Models\CustomerImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerService{
    private $customer;

    private $storage_static_files = "public/images/customers";

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function imageUploader($file)
    {
        $imageInfo = @getimagesize($file);

        if ($imageInfo && ($imageInfo[2] == IMAGETYPE_JPEG || $imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF)) {
            $hashedImage = Str::random(10) . '.' . $file->getClientOriginalExtension();

            $imageData = [
                'path' => config('app.url') . '/storage/images/customers/',
                'name' => $hashedImage,
            ];

            if (!Storage::exists($this->storage_static_files)) {
                Storage::makeDirectory($this->storage_static_files);
            }

            if ($imageData) {
                $image = new CustomerImage($imageData);
                $savedImage = $this->customer->image()->save($image);
                if ($savedImage) {
                    $storedFile = Storage::putFileAs($this->storage_static_files, $file, $hashedImage);
                }
                return $image;
            } else {
                return false;
            }
        }
    }

    public function getEditData(){
        $customer = $this->customer
        ->with('image')
        ->find($this->customer->id);

        $result = [
            'image' => $customer->image
        ];

        return $result;

    }

}

?>