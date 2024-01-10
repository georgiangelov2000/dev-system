<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\InvoicePurchase;


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
        
        if (($purchase && $status === self::INIT_STATUS) || $isNewPurchase) {
            $invNumber = $data['invoice_number'];
            $invDate = now()->parse($data['invoice_date']);

            // Update purchase or create Purchase;
            $purchase->name = $data['name'];
            $purchase->supplier_id = $data['supplier_id'];
            $purchase->notes = $data['notes'] ?? '';
            
            // assign updated values to purchase
            $purchase->price = $data['price'];
            $purchase->discount_percent = intval($data['discount_percent']) ?? 0;
        
            // Update amount                
            if ($data['quantity'] > 0) {
                $purchase->quantity = $data['quantity'];
            }
            
            // Check order amount
            $ordersQuantity = $purchase->orders->sum('sold_quantity');
            
            $calculateNewInitialQuantity = ($purchase->quantity  + $ordersQuantity);
            
            if ($calculateNewInitialQuantity < $ordersQuantity) {
                throw new \Exception("Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.");
            };

            $finalQuantity = ($calculateNewInitialQuantity - $ordersQuantity);
            $purchase->initial_quantity = $calculateNewInitialQuantity;
            $purchase->quantity = $finalQuantity;

            $purchase = $this->calculatePrices($purchase);

            // Update code
            $purchase->code = $data['code'];

            // Update dates
            $purchase->expected_delivery_date = now()->parse($data['expected_delivery_date']);
            $expected_date_of_payment = now()->parse($data['expected_date_of_payment']);
            $purchase->is_it_delivered = self::INITIAL_DELIVERED;
            $purchase->weight = $data['weight'];
            $purchase->height = $data['height'];
            $purchase->color = $data['color'];

            // Check for uploaded image
            if ($file) {
                FunctionsHelper::imageUploader($file, $purchase, $this->dir,'image_path');
            }
            
                                    
            // Create or update current purchase
            $purchase->save();

            $this->createOrUpdatePayment(
                $purchase,
                $expected_date_of_payment,
                $invNumber,
                $invDate
            );

            // Sync relationships for purchase
            FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'category_id', 'categories');
            FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'subcategories', 'subcategories');
            FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'brands', 'brands');

        }
        
        return $purchase;
    }

    public function purchaseMassEditProcessing(Purchase $purchase, array $data)
    {   
        $expectedDateOfPayment = null;
        
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

        if($data['expected_date_of_payment']) {
            $expectedDateOfPayment = now()->parse($data['expected_date_of_payment']);
        }

        if($data['expected_delivery_date']) {
            $purchase->expected_delivery_date = now()->parse($data['expected_delivery_date']);
        }
        
        $ordersQuantity = $purchase->orders->sum('sold_quantity');
        if ($purchase->initial_quantity < $ordersQuantity) {
            throw new \Exception("Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.");
        };

        $finalQuantity = ($purchase->initial_quantity - $ordersQuantity);
        $purchase->quantity = $finalQuantity;

        $purchase = $this->calculatePrices($purchase);

        $purchase->save();

        // Sync relationships for purchase
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'category_id', 'categories');
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'sub_category_ids', 'subcategories');
        FunctionsHelper::syncRelationshipIfNotEmpty($purchase, $data, 'brands', 'brands');

        $this->createOrUpdatePayment($purchase,$expectedDateOfPayment);

        return $purchase;
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
        $discount_price = FunctionsHelper::calculatedDiscountPrice(
            $model->price, 
            $model->discount_percent
        );

        // Calculate the total price
        $total_price = FunctionsHelper::calculatedFinalPrice(
            $discount_price, 
            $model->initial_quantity
        );

        // Calculate the original price
        $original_price = FunctionsHelper::calculatedFinalPrice(
            $model->price, 
            $model->initial_quantity
        );

        $model->discount_price = $discount_price;
        $model->total_price = $total_price;
        $model->original_price = $original_price;
        
        return $model;
    }

    private function createOrUpdatePayment(
        $purchase, 
        $expected_date_of_payment = null,
        $invoiceNumber = null,
        $invoiceDate = null,
    ): PurchasePayment
    {
        $payment = $purchase->payment ? $purchase->payment : new PurchasePayment();
        
        // Generate an alias for the payment based on the purchase's delivery date
        $alias = $this->getAlias($purchase);

        $payment->alias = $alias;
        $payment->quantity = $purchase->quantity;
        $payment->price = $purchase->total_price;

        if(isset($expected_date_of_payment)) {
            $payment->expected_date_of_payment = $expected_date_of_payment;
        }

        if(!$payment->exists) {
            $payment->payment_status = self::INIT_STATUS;
        }

        $payment->save();

        $invoice = $payment->invoice ? $payment->invoice : new InvoicePurchase();

        if($invoiceNumber) {
            $invoice->invoice_number = $invoiceNumber;
        }
        if($invoiceDate) {
            $invoice->invoice_date = $invoiceDate;
        }
        
        $invoice->price = $payment->price;
        $invoice->quantity = $payment->quantity;

        $invoice->save();

        return $payment;
    }

    private function getAlias($purchase)
    {
        // Generate an alias based on the delivery date of the purchase
        $alias = now()->parse($purchase->expected_delivery_date)->format('F j, Y');
        // Replace spaces and commas with underscores
        $alias = str_replace([' ', ','], ['_', ''], $alias);
        // Convert alias to lowercase
        $alias = strtolower($alias);

        // Return the generated alias
        return $alias;
    }
}
