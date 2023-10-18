<?php

namespace App\Factory\Imports;

use App\Factory\CsvImporter;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

final class CustomerCsvImporter extends CsvImporter
{
    /**
     * Array of validation rules for the provider.
     *
     * @var array
     */
    private $keys = [
        "name" => "required|string",
        "email" => "required|email",
        "phone" => "required|string",
        "address" => "required|string",
        "website" => "nullable|string",
        "zip" => "required|string",
        'country_id' => "required|integer",
        'state_id' => "required|integer",
        "notes" => "nullable|string",
    ];

    /**
     * Performs validation and creates providers from the provided data.
     *
     * @param array $data
     * @return array
     */
    public function initValidation(array $data): array
    {
        $createdCustomers = [];

        foreach ($data as $key => $value) {

            // Performs validation and creates a new provider
            $validator = Validator::make($value, $this->keys);

            if ($validator->fails()) {
                return ['error' => 'Data has not been inserted'];
            }

            // Checks for location availability before validation
            if (!$this->checkForLocation($value['country_id'], $value['state_id'])) {
                return ['error' => "Provided location data has not been found!"];
            }

            // Create supplier
            $customer = Customer::create($validator->validated());

            $createdCustomers[] = $customer;
        }
        return $createdCustomers;
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
}
