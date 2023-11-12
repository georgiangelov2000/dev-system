<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;
use App\Models\Purchase;


class PurchaseService
{
    private $statuses;
    private $dir = 'public/images/products';
    const INIT_STATUS = 2;
    const INITIAL_DELIVERED = 0;

    public function __construct()
    {
        $this->statuses = config('statuses.payment_statuses');
    }

    public function purchaseProcessing(array $data, $purchase = null)
    {        

        // Check if $data['image'] exists and set it to $file
        $file = $data['image'] ?? null;

        $purchase = $purchase ? $purchase : new Purchase;
        $isNewPurchase = !$purchase->exists; // Check if it's a new purchase
        $status = $isNewPurchase
            ? FunctionsHelper::statusValidation(self::INIT_STATUS, $this->statuses)
            : FunctionsHelper::statusValidation($purchase->payment->payment_status, $this->statuses);
        $expected_date_of_payment = !$isNewPurchase ? $purchase->payment->expected_date_of_payment : null;
        
        // Update purchase or create Purchase;
        $purchase->name = $data['name'];
        $purchase->supplier_id = $data['supplier_id'];
        $purchase->notes = $data['notes'] ?? '';
        
        if (($purchase && $status === self::INIT_STATUS) || $isNewPurchase) {
            $prices = null;
            
            // assign updated values to purchase
            $purchase->price = $data['price'];
            $purchase->discount_percent = intval($data['discount_percent']) ?? 0;
        
            // Update amount
            if ($data['quantity'] > 0) {
                $purchase->quantity = $data['quantity'];
                $purchase->initial_quantity = $data['quantity'];
            }

            // Check order amount
            $ordersQuantity = $purchase->orders->sum('sold_quantity');

            if ($purchase->initial_quantity < $ordersQuantity) {
                throw new \Exception("Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.");
            };
            $finalQuantity = ($purchase->initial_quantity - $ordersQuantity);
            $purchase->quantity = $finalQuantity;

            // Calculate prices
            $prices = $this->calculatePrices(
                $purchase->price,
                $purchase->discount_percent,
                $purchase->initial_quantity
            );
            
            // Update prices
            $purchase->total_price = $prices['total_price'];
            $purchase->original_price = $prices['original_price'];
            $purchase->discount_price = $prices['discount_price'];

            // Update code
            $purchase->code = $data['code'];

            // Update dates
            $purchase->expected_delivery_date = now()->parse($data['expected_delivery_date']);
            $expected_date_of_payment = now()->parse($data['expected_date_of_payment']);
            $purchase->is_it_delivered = self::INITIAL_DELIVERED;

            $this->createOrUpdatePayment($purchase ,$expected_date_of_payment);
        }

        // Check for uploaded image
        if ($file) {
            FunctionsHelper::imageUploader($file, $purchase, $this->dir);
        }

        // Create or update current purchase
        $purchase->save();

        // Sync relationships for purchase
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'category_id', 'categories');
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'subcategories', 'subcategories');
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'brands', 'brands');

        return $purchase;
    }

    public function purchaseMassEditProcessing(array $data, $id)
    {
        $purchase = Purchase::find($id);

        if ($data['quantity'] && is_int(intval($data['quantity']))) {
            $purchase->quantity = $data['quantity'];
            $purchase->initial_quantity = $data['quantity'];
        }
        if ($data['price'] && is_numeric($data['price'])) {
            $purchase->price = $data['price'];
        }

        if ($data['discount_percent'] && is_int(intval($data['discount_percent']))) {
            $purchase->discount_percent = $data['discount_percent'];
        }

        $ordersQuantity = $purchase->orders->sum('sold_quantity');
        if ($purchase->initial_quantity < $ordersQuantity) {
            throw new \Exception("Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.");
        };
        $finalQuantity = ($purchase->initial_quantity - $ordersQuantity);
        $purchase->quantity = $finalQuantity;

        $prices = $this->calculatePrices(
            $purchase->price,
            $purchase->discount_percent,
            $purchase->quantity
        );

        $purchase->total_price = $prices['total_price'];
        $purchase->original_price = $prices['original_price'];
        $purchase->discount_price = $prices['discount_price'];

        $purchase->save();

        // Sync relationships for purchase
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'category_id', 'categories');
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'sub_category_ids', 'subcategories');
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'brands', 'brands');

        $this->createOrUpdatePayment($purchase);

        return $purchase;
    }

    public function calculatePrices($price, $discount, $quantity): array
    {
        // Calculate discounted price using helper method
        $discount_price = FunctionsHelper::calculatedDiscountPrice(floatval($price), $discount);
        // Calculate total price after applying discount for given quantity
        $total_price = FunctionsHelper::calculatedFinalPrice(floatval($discount_price), $quantity);
        // Calculate total price without discount for given quantity
        $original_price = FunctionsHelper::calculatedFinalPrice(floatval($price), $quantity);

        // Return an array containing calculated prices
        return compact('discount_price', 'total_price', 'original_price');
    }

    private function createOrUpdatePayment($purchase, $expected_date_of_payment)
    {
        // Generate an alias for the payment based on the purchase's delivery date
        $alias = $this->getAlias($purchase);
        
        // Prepare payment data
        $paymentData = [
            'alias' => $alias,
            'quantity' => $purchase->initial_quantity,
            'price' => $purchase->total_price,
            'expected_date_of_payment' => $expected_date_of_payment
        ];
                
        // Check if a payment record already exists for the purchase
        $existingPayment = $purchase->payment;

        // If no payment record exists, create one
        if (!$existingPayment) {
            $paymentData['payment_status'] = self::INIT_STATUS;
            $payment = $purchase->payment()->create($paymentData);
        } else {
            // Update the existing payment record with new data
            $existingPayment->update($paymentData);
            $payment = $existingPayment;
        }

        // Update or create invoice record associated with the payment
        $payment->invoice()->updateOrCreate([], [
            'price' => $payment->price,
            'quantity' => $payment->quantity
        ]);

        return $payment;
    }

    private function getAlias($purchase)
    {
        // Generate an alias based on the delivery date of the purchase
        $alias = $purchase->expected_delivery_date->format('F j, Y');
        // Replace spaces and commas with underscores
        $alias = str_replace([' ', ','], ['_', ''], $alias);
        // Convert alias to lowercase
        $alias = strtolower($alias);

        // Return the generated alias
        return $alias;
    }
}
