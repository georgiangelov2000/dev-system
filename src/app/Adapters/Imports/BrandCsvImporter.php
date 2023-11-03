<?php

namespace App\Adapters\Imports;

use App\Adapters\CsvImporter;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class BrandCsvImporter extends CsvImporter
{
    /**
     * Array of validation rules for the category.
     *
     * @var array
     */
    private $keys = [
        'name' => 'required|string',
        'description' => 'nullable|string',
    ];

    /**
     * Performs validation and creates brands from the provided data.
     * @param array $data
     * 
     * @return array An array of created brands.
     */
    public function initValidation(array $data): array
    {
        // Initialize array for brands storing
        $createdBrands = [];
        
        // Performs validation and creates a new brand
        foreach ($data as $key => $value) {
            
            // Create a new validator instance for the current data using predefined validation rules
            $validator = Validator::make($value, $this->keys);

            // Check for errors
            if ($validator->fails()) {
                // Return an error message if validation fails for the current data
                return ['error' => 'Data has not been inserted'];
            }

            // Validation successful; create a new brand using the validated data
            $brand = Brand::create($validator->validated());
            
            // Add the created brand to the array of created brands
            $createdBrands[] = $brand;
        }

        // Return the array of created brands
        return $createdBrands;
    }
}
