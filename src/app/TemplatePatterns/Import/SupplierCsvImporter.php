<?php

namespace App\TemplatePatterns\Import;

use App\Models\Category;
use App\Models\Country;
use App\Models\Supplier;
use App\TemplatePatterns\Import\CsvImporter;
use Illuminate\Support\Facades\Validator;

class SupplierCsvImporter extends CsvImporter
{

    /**
     * Array of validation rules for the provider.
     *
     * @var array
     */
    private $keys = [
        "name" => "required|string",
        "email" => "unique|required|email",
        "phone" => "required|string",
        "address" => "required|string",
        "website" => "nullable|string",
        "zip" => "required|string",
        'country_id' => "required|integer",
        'state_id' => "required|integer",
        "notes" => "nullable|string",
        "categories" => "required|array",
        'image_path' => "nullable|string"
    ];


    /**
     * Processes the data from the CSV file.
     *
     * @param string $file
     * @return array
     */
    public function processData($file)
    {
        $csvData = $this->parseCSV($file);
        return $csvData;
    }

    /**
     * Performs validation and creates providers from the provided data.
     *
     * @param array $data
     * @return array
     */
    public function initValidation(array $data): array
    {
        $createdSuppliers = [];
  
        foreach ($data as $row) {

            // Checks and converts the categories before validation
            if (isset($row['categories'])) {
                $row['categories'] = explode('&', $row['categories']);
                if (!$this->checkForCategory($row['categories'])) {
                    return ['error' => "Provided categories have not been found!"];
                }
            }

            // Performs validation and creates a new provider
            $validator = Validator::make($row, $this->keys);

            if ($validator->fails()) {
                return ['error' => 'Data has not been inserted'];
            }

            // Checks for location availability before validation
            if (!$this->checkForLocation($row['country_id'], $row['state_id'])) {
                return ['error' => "Provided location data has not been found!"];
            }

            // Create supplier
            $supplier = Supplier::create($validator->validated());

            // Assign categories to current supplier
            $supplier->categories()->sync($row['categories']);

            $createdSuppliers[] = $supplier;
        }

        return $createdSuppliers;
    }

    /**
     * Checks the presence of the location (country and region) in the database.
     *
     * @param string $countryId
     * @param string $stateId
     * @return bool
     */
    private function checkForLocation(string $countryId, string $stateId): bool
    {
        return Country::where('id', $countryId)->whereHas('states', function ($query) use ($stateId) {
            $query->where('id', $stateId);
        })->exists();
    }

    /**
     * Checks the availability of the categoryIds in the database.
     *
     * @param array $categoryIds
     * @return bool
     */
    private function checkForCategory(array $categoryIds): bool
    {
        return Category::whereIn('id', $categoryIds)->exists();
    }
}
