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
    public function calculatePrices($price, $discount, $quantity): array
    {
        // Calculate the discounted price
        $discount_price = $this->helper->calculatedDiscountPrice($price, $discount);

        // Calculate the total price
        $total_price = $this->helper->calculatedFinalPrice($discount_price, $quantity);

        // Calculate the original price
        $original_price = $this->helper->calculatedFinalPrice($price, $quantity);

        return compact('discount_price', 'total_price', 'original_price');
    }

    /**
     * Generate an alias based on the provided order's package extension date or date of sale.
     *
     * If package_extension_date is available, it will be used; otherwise, date_of_sale will be used.
     *
     * @param \App\Models\Order $order The order instance for which to generate the alias.
     * @return string The generated alias.
     */
    public function getAlias($order)
    {
        // Determine the alias based on package_extension_date or date_of_sale
        $aliasDate = $order->package_extension_date
            ? now()->parse($order->package_extension_date)->format('F j, Y')
            : now()->parse($order->date_of_sale)->format('F j, Y');

        // Replace spaces and commas with underscores, and convert to lowercase
        return strtolower(str_replace([' ', ','], ['_', ''], $aliasDate));
    }
}
