<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierApiController extends Controller {

    public function getData(Request $request) {

        $country = $request->country;
        $state = $request->state;
        $category = $request->category;

        $supplierQuery = $this->buildSupplierQuery();

        if ($country && $country !== '9999') {

            $this->supplierByCountry($country, $supplierQuery);

            if ($state) {
                $this->supplierByState($state, $supplierQuery);
            }
        }

        if ($category) {
            $this->supplierByCategory($category, $supplierQuery);
        }

        $result = $this->getSuppliers($supplierQuery);

        return response()->json(['data' => $result]);
    }

    private function buildSupplierQuery() {
        return Supplier::query()
                        ->with([
                            'states:id,name',
                            'image:id,supplier_id,path,name',
                            'country:id,name',
                        ])
                        ->select([
                            'id',
                            'name',
                            'email',
                            'phone',
                            'address',
                            'zip',
                            'website',
                            'notes',
                            'state_id',
                            'country_id'
                        ])
                        ->with(['categories' => function ($query) {
                                $query->select('suppliers_categories.id', 'name');
                            }]);
    }

    private function supplierByCountry($country, $query) {
        $query->where('country_id', $country);
    }

    private function supplierByState($state, $query) {
        $query->where('state_id', $state);
    }

    private function supplierByCategory($category, $query) {
        $query->whereHas('categories', function ($query) use ($category) {
            $query->whereIn('categories.id', $category);
        });
    }

    private function getSuppliers($query) {
        return $query->get();
    }

}
