<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FunctionsHelper
{
    public static function calculatedDiscountPrice($price, $discount)
    {

        if (($price && $discount) && (is_numeric($price) && is_numeric($discount))) {
            $finalPrice = $price - (($price * $discount) / 100);
        } else {
            $finalPrice = $price;
        }

        return number_format($finalPrice, 2);
    }

    public static function calculatedFinalPrice($finalSingleSoldPrice, $quantity)
    {

        if (($finalSingleSoldPrice && $quantity) && (is_numeric($finalSingleSoldPrice) && is_numeric($quantity))) {
            $finalPrice = ($finalSingleSoldPrice * $quantity);
        } else {
            $finalPrice = 0;
        }

        $finalPrice = number_format($finalPrice, 2);

        return number_format($finalPrice, 2);
    }

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

    public static function getImagePath($path): string
    {
        return Storage::url($path);
    }
}
