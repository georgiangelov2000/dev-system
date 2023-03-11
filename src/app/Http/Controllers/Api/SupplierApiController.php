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
        
        $suppliers = Supplier::query()
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
                    $query->select('suppliers_categories.id','name');
                }]);

        if (isset($country) && $country && $country !== '9999') {
            $suppliers->where('country_id', $country);
            if (isset($state) && $state !== 'all') {
                $suppliers->where('state_id', $state);
            }
        }

        if (isset($category) && $category) {
            $suppliers->whereHas('categories', function ($query) use ($category) {
                $query->whereIn('categories.id', $category);
            });
        }

        $result = $suppliers->get();

        return response()->json(['data' => $result]);
    }

}
