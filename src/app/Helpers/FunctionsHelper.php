<?php

namespace App\Helpers;

use App\Models\Settings;
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

        return number_format($finalPrice, 2, '.', '');
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
    public static function imageUploader(UploadedFile $file, $model, $dir, $uploadedColumn)
    {
        try {
            if ($file->isValid()) {
                $hashedImage = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

                // Check if the model has an existing image path
                if (isset($model->$uploadedColumn)) {

                    // Get the stored file path without the '/storage' prefix
                    $storedFile = str_replace('/storage', '', $model->$uploadedColumn);

                    // Check if the stored file exists and delete it if it does
                    if (Storage::disk('public')->exists($storedFile)) {
                        Storage::disk('public')->delete($storedFile);
                    }
                }

                // Upload the new image file
                Storage::putFileAs($dir, $file, $hashedImage);
                $model->$uploadedColumn = self::getImagePath($dir) . '/' . $hashedImage;

                return $model;
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

    /**
     * Return structured settings result
     */
    public static function settings()
    {
        $settingsInformation = Settings::where('type', 1)->first();
        
        if ($settingsInformation) {
            $result = json_decode($settingsInformation->settings, true);
        } else {
            $result = Settings::getStruct();
        }

        return $result;
    }

    public static function dateRange($date)
    {
        if (!empty($date)) {
            $dates = explode(" - ", $date);
            $date1 = $dates[0];
            $date2 = $dates[1];

            $date1_formatted = date('Y-m-d 23:59:59', strtotime($date1));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date2));

            if (strtotime($date1) !== false && strtotime($date2) !== false) {
                return [
                    $date1_formatted,
                    $date2_formatted
                ];
            }
        }

        return null;
    }

    public static function dateToString($dateStart, $dateEnd): ?string
    {
        // Format the date range for the response
        if ($dateStart && $dateEnd) {
            return date('F j, Y', strtotime($dateStart)) . ' - ' . date('F j, Y', strtotime($dateEnd));
        }

        return null;
    }

    // CSV Importing Helpers
    public static function processArrayField($value, $fieldName): array
    {
        $fieldValue = isset($value[$fieldName]) ? trim($value[$fieldName]) : '';
        
        if (preg_match('/^\d+&\d+$/', $fieldValue)) {
            return explode('&', $fieldValue);
        } elseif (ctype_digit($fieldValue)) {
            return [$fieldValue];
        } else {
            return [];
        }
    }

    /**
     * Status validation.
     *
     * @param int $status
     * @return int
     */
    public static function statusValidation(int|string $status, array $statuses): ?int
    {
        return array_key_exists($status, $statuses) ? $status : null;
    }

    /**
     * Syncs a relationship for the given model if the key exists in the data array and is not empty.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @param  string  $relationshipKey
     * @return void
     */
    public static function syncRelationshipIfNotEmpty($model, $data, $relationshipKey, $relationship)
    {
        // Check if the relationship key exists in the data array and is not empty
        if (array_key_exists($relationshipKey, $data) && !empty($data[$relationshipKey])) {
            // Sync the relationship data using the provided key
            $model->{$relationship}()->sync($data[$relationshipKey]);
        }
    }

    public static function logData($messageKey, $actionKey, $name, $user, $created = null, $updated = null, $deleted = null){
        $logs = config('logs');
        $message = $action = '';
        $user_id = $user->id;

        if(array_key_exists($messageKey, $logs['messages'])) {
            $message = str_replace(["{{name}}", "{{user}}"], [$name, $user->username], $logs['messages'][$messageKey]);
            if(str_contains($message,"{{created_at}}")) {
                $message = str_replace("{{created_at}}", $created, $message);
            } elseif(str_contains($message,"{{updated_at}}")) {
                $message = str_replace("{{updated_at}}", $updated, $message);
            } elseif(str_contains($message,"{{deleted_at}}")) {
                $message = str_replace("{{deleted_at}}", $deleted, $message);
            }
        }
        
        $action = array_key_exists($actionKey, $logs['actions']) ? $logs['actions'][$actionKey] : '';
    
        return compact('action', 'message', 'user_id');
    }    
}
