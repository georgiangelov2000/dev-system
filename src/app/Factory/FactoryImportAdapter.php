<?php

namespace App\Factory;

use App\Adapters\Imports\SupplierCsvImporter;
use App\Adapters\Imports\BrandCsvImporter;
use App\Adapters\Imports\CategoryCsvImporter;
use App\Adapters\Imports\PurchaseCsvImporter;
use App\Adapters\Imports\CustomerCsvImporter;

class FactoryImportAdapter

{

    private static $types = [
        'supplier','customer','brand','purchase','category'
    ];

    private function __construct()
    {
        
    }

    public static function select(string $type)
    {
        if (in_array($type, self::$types)) {
            switch ($type) {
                case 'supplier':
                    return new SupplierCsvImporter();
                case 'customer':
                    return new CustomerCsvImporter();
                case 'brand':
                    return new BrandCsvImporter();
                case 'purchase':
                    return new PurchaseCsvImporter();
                case 'category':
                    return new CategoryCsvImporter();
                default:
                    return null;
            }
        } else {
            return null;
        }
    }
    

}
