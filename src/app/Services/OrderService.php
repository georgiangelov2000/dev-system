<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;

class OrderService
{
    protected $helper;

    public function __construct(FunctionsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Calculate prices based on price, discount, and quantity.
     *
     * @param float $price The original price.
     * @param float $discount The discount percentage.
     * @param int $quantity The quantity of items.
     * @return array An array containing discount price, total price, and original price.
     */
    public function calculatePrices($model)
    {
        // Calculate the discounted price
        $discount_price = $this->helper->calculatedDiscountPrice(
            $model->single_sold_price, 
            $model->discount_percent
        );

        // Calculate the total price
        $total_price = $this->helper->calculatedFinalPrice(
            $discount_price, 
            $model->sold_quantity
        );

        // Calculate the original price
        $original_price = $this->helper->calculatedFinalPrice(
            $model->single_sold_price, 
            $model->sold_quantity
        );

        $model->discount_single_sold_price = $discount_price;
        $model->total_sold_price = $total_price;
        $model->original_sold_price = $original_price;
        
        return $model;
    }

    /**
     * Generate an alias based on the provided order's package extension date or expected_delivery_date.
     *
     * If package_extension_date is available, it will be used; otherwise, expected_delivery_date will be used.
     *
     * @param \App\Models\Order $order The order instance for which to generate the alias.
     * @return string The generated alias.
     */
    public function getAlias($order)
    {
        // Determine the alias based on package_extension_date or expected_delivery_date
        $aliasDate = $order->package_extension_date
            ? now()->parse($order->package_extension_date)->format('F j, Y')
            : now()->parse($order->expected_delivery_date)->format('F j, Y');

        // Replace spaces and commas with underscores, and convert to lowercase
        return strtolower(str_replace([' ', ','], ['_', ''], $aliasDate));
    }
}
