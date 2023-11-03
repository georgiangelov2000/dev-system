<?php

namespace App\Adapters\Imports;

use App\Adapters\CsvImporter;
use App\Helpers\FunctionsHelper;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\DB;
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
        'discount_percent' => 'nullable|integer|min:0',
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
        $createdPurchases = [];
        $service = new PurchaseService();

        foreach ($data as $key => $value) {
            $value['subcategories'] = FunctionsHelper::processArrayField($value, 'subcategories');
            $value['brands'] = FunctionsHelper::processArrayField($value, 'brands');

            $validator = Validator::make($value, $this->keys);

            if ($validator->fails()) {
                throw new \Exception("Data has not been inserted");
            }

            $supplier = Supplier::find($value['supplier_id']);
            $category = Category::find($value['category_id']);

            if (!$supplier || !$category) {
                throw new \Exception("Supplier or Category not found");
            }

            $validatedData = $validator->validated();

            try {
                DB::beginTransaction();

                $createdPurchases[] = $service->purchaseProcessing($validatedData);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return $createdPurchases;
    }
}
