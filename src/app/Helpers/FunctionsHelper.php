<?php

namespace App\Helpers;

class FunctionsHelper
{
    public static function calculatedDiscountPrice($price, $discount)
    {
        $discountAmount = 0;
        $finalPrice = 0;

        if (($price && $discount) && (is_numeric($price) && is_numeric($discount))) {
            $discountAmount = (($price * $discount) / 100);
            $finalPrice = ($price - $discountAmount);
        } else {
            $finalPrice = $price;
        }

        return $finalPrice;
    }

    public static function calculatedFinalPrice($finalSingleSoldPrice, $quantity)
    {
        $finalPrice = 0;

        if (($finalSingleSoldPrice && $quantity) && (is_numeric($finalSingleSoldPrice) && is_numeric($quantity))) {
            $finalPrice = ($finalSingleSoldPrice * $quantity);
        }

        return $finalPrice;
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
}
