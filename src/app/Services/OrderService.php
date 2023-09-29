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
        $discountPrice = $this->helper->calculatedDiscountPrice($price, $discount);

        // Calculate the total price
        $totalPrice = $this->helper->calculatedFinalPrice($discountPrice, $quantity);

        // Calculate the original price
        $originalPrice = $this->helper->calculatedFinalPrice($price, $quantity);

        return [
            'discount_price' => $discountPrice,
            'total_price' => $totalPrice,
            'original_price' => $originalPrice
        ];
    }

    public function createOrUpdatePayment($order)
    {
        // Generate an alias based on the order's date
        $alias = $this->getAlias($order);

        $paymentData = [
            'alias' => $alias,
            'quantity' => $order->sold_quantity,
            'price' => $order->total_sold_price,
            'date_of_payment' => $this->dateOfPayment($order)
        ];

        // Update or create the payment record
        $payment = $order->payment()->updateOrCreate([], $paymentData);

        // Update or create the associated invoice record
        $payment->invoice()->updateOrCreate([], [
            'price' => $payment->price,
            'quantity' => $payment->quantity
        ]);
    }

    private function getAlias($order)
    {
        // Determine the alias based on package_extension_date or date_of_sale
        $aliasDate = $order->package_extension_date
            ? now()->parse($order->package_extension_date)->format('F j, Y')
            : now()->parse($order->date_of_sale)->format('F j, Y');

        return strtolower(str_replace([' ', ','], ['_', ''], $aliasDate));
    }

    private function dateOfPayment($order)
    {
        // Determine the date of payment based on package_extension_date or date_of_sale
        return $order->package_extension_date
            ? $order->package_extension_date
            : $order->date_of_sale;
    }
}
