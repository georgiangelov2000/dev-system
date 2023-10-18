<?php

namespace App\Factory\Imports;

use App\Factory\CsvImporter;
use App\Helpers\FunctionsHelper;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

final class PurchaseCsvImporter extends CsvImporter
{
    /**
     * Array of validation rules for the purchase.
     *
     * @var array
     */
    private $keys = [
        "name" => "required|string",
        "code" => "required|string",
        "supplier_id" => "required|integer|not_in:0",
        "category_id" => "required|integer|not_in:0",
        "subcategories" => "nullable|array",
        "notes" => "nullable|string",
        "brands" => "nullable|array",
        "delivery_date" => 'nullable|date',
        'expected_date_of_payment' => 'required|date',
        'discount_percent' => 'nullable|integer|min:0|default',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
    ];

    /**
     * Performs validation and creates purchases from the provided data.
     *
     * @param array $data
     * @return array
     */
    public function initValidation(array $data): array
    {
        foreach ($data as $key => $value) {

            // Create array of subcategories if they are exist
            $value['subcategories'] = FunctionsHelper::processArrayField($value, 'subcategories');

            // Create array of brands if they are exist
            $value['brands'] = FunctionsHelper::processArrayField($value, 'brands');

            // Performs validation and creates a new provider
            $validator = Validator::make($value, $this->keys);

            // Check if validation fails
            if ($validator->fails()) {
                // Return an error message if validation fails for the current data
                return ['error' => 'Data has not been inserted'];
            }

            // Find supplier and category based on IDs
            $supplier = Supplier::find($value['supplier_id']);
            $category = Category::find($value['category_id']);

            // Check if supplier and category exist
            if (!$supplier || !$category) {
                return [
                    'error' => "Supplier or Category not found"
                ];
            }

            // Calculate prices using helper functions
            $prices = $this->finalPrices($value['price'], $value['discount_percent'], $value['quantity']);

            // Create Purchase with validated data and calculated prices
            $purchaseData = $validator->validated() + $prices;
            $purchase = Purchase::create($purchaseData);

            // Sync relationships (categories, subcategories, brands) if provided in the data
            if (!empty($value['category_id'])) {
                $purchase->categories()->sync($value['category_id']);
            }

            if (!empty($value['sub_category_ids'])) {
                $purchase->subcategories()->sync($value['sub_category_ids']);
            }

            if (!empty($value['brands'])) {
                $purchase->brands()->sync($value['brands']);
            }
        }
    }

    /**
     * Calculate final prices based on the original price, discount, and quantity.
     *
     * @param float $price
     * @param int|null $discount
     * @param int $quantity
     * @return array
     */
    private function finalPrices($price, $discount, $quantity): array
    {
        // Calculate discounted price and total price using helper functions
        $discountPrice = FunctionsHelper::calculatedDiscountPrice($price, $discount);
        $totalPrice = FunctionsHelper::calculatedFinalPrice($discountPrice, $quantity);
        $originalPrice = FunctionsHelper::calculatedFinalPrice($price, $quantity);
        // Return an array containing calculated prices
        return compact('discount_price', 'total_price', 'original_price');
    }
}
