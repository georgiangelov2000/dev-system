<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FunctionsHelper
{
    /**
     * Calculate the final price after applying a discount percentage.
     *
     * @param float $price
     * @param float $discount
     * @return string
     */
    public static function calculatedDiscountPrice($price, $discount)
    {
        if (($price && $discount) && (is_numeric($price) && is_numeric($discount))) {
            $finalPrice = $price - (($price * $discount) / 100);
        } else {
            $finalPrice = $price;
        }

        return number_format($finalPrice, 2);
    }

    /**
     * Calculate the final price by multiplying the price and quantity.
     *
     * @param string $price
     * @param int $quantity
     * @return string
     */
    public static function calculatedFinalPrice($price, $quantity)
    {
        // Remove commas from the price if they exist
        $price = str_replace(',', '', $price);

        if (is_numeric($price) && is_numeric($quantity)) {
            $finalPrice = ($price * $quantity);
        } else {
            $finalPrice = 0;
        }

        // Format the final price with two decimal places using a period as the decimal separator
        return number_format($finalPrice, 2, '.', '');
    }

    /**
     * Convert a date range string to an array of timestamps.
     *
     * @param string $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function dateRangeConverter($data)
    {
        $dates = explode(" - ", $data);

        if (count($dates) !== 2) {
            throw new \InvalidArgumentException("Invalid date range format");
        }

        $startTimestamp = strtotime($dates[0]);
        $endTimestamp = strtotime($dates[1]);

        if ($startTimestamp === false || $endTimestamp === false) {
            throw new \InvalidArgumentException("Invalid date format");
        }

        if ($startTimestamp > $endTimestamp) {
            // Swap start and end timestamps if they are reversed
            list($startTimestamp, $endTimestamp) = [$endTimestamp, $startTimestamp];
        }

        return [$startTimestamp, $endTimestamp];
    }

    /**
     * Get the image path for a given path.
     *
     * @param string $path
     * @return string
     */
    public static function getImagePath($path): string
    {
        return Storage::url($path);
    }

    /**
     * Upload an image file, replace the existing image if it exists.
     *
     * @param UploadedFile $file
     * @param object $model
     * @param string $dir
     * @throws \Exception
     */
    public static function imageUploader(UploadedFile $file, $model, $dir)
    {
        try {
            if ($file->isValid()) {
                $hashedImage = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                
                // Check if the model has an existing image path
                if (isset($model->image_path)) {

                    // Get the stored file path without the '/storage' prefix
                    $storedFile = str_replace('/storage', '', $model->image_path);
                    
                    // Check if the stored file exists and delete it if it does
                    if (Storage::disk('public')->exists($storedFile)) {
                        Storage::disk('public')->delete($storedFile);
                    }
                }

                // Upload the new image file
                Storage::putFileAs($dir, $file, $hashedImage);
                $model->image_path = self::getImagePath($dir) . '/' . $hashedImage;
            } else {
                throw new \Exception("Invalid file uploaded.");
            }
        } catch (\Exception $e) {
            Log::error("Error in imageUploader: " . $e->getMessage());
        }
    }

    /**
     * Delete the image associated with a model.
     *
     * @param object $model
     * @return bool
     */
    public static function deleteImage($model)
    {
        $storedFile = str_replace('/storage', '', $model->image_path);
        
        // Check if the image path exists in storage
        if (Storage::disk('public')->exists($storedFile)) {
            // If it exists, delete the image
            Storage::disk('public')->delete($storedFile);

            return true;
        }

        return false;
    }
}
