<?php
namespace App\Services;

class SupplierService {

    public function syncCategories(array $categories, $supplier){
        if(!empty($categories)) {
            $supplier->categories()->sync($categories);
        }
    }

}