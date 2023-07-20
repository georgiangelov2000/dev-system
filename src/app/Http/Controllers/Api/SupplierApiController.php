<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierApiController extends Controller {

    public function getData(Request $request) {

        $country = isset($request->country) ? $request->country : null;
        $state = isset($request->state)  ? $request->state : null;
        $category = isset($request->category) ? $request->category : null;
        $search = isset($request->search) ? $request->$request : null;
        $select_json = isset($request->select_json) && $request->select_json ? $request->select_json : null;

        $offset = $request->input('start', 0);  
        $limit = $request->input('length', 10);

        $supplierQuery = Supplier::query();

        if($search) {
            $supplierQuery->where('name', 'LIKE', '%'.$search.'%');
        }
        if ($country) {
            $this->supplierByCountry($country, $supplierQuery);
            if ($state) {
                $this->supplierByState($country, $state, $supplierQuery);
            }
        }
        if ($category) {
            $this->supplierByCategory($category, $supplierQuery);
        }
        if(isset($request->with_orders) && $request->with_orders == true) {
            $supplierQuery->has('purchases');
        }
        if($select_json) {
            return response()->json(
                $supplierQuery->select('id','name')->get()
            );
        }

        $supplierQuery ->with([
            'state:id,name',
            'image:id,supplier_id,path,name',
            'country:id,name,short_name',
            'categories:id,name'
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
        ])->withCount('purchases');

        $totalRecords = Supplier::count();
        $filteredRecords = $supplierQuery->count();
        $result = $supplierQuery->skip($offset)->take($limit)->get();
        
        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    private function supplierByCountry($country, $query) {
        $query->where('country_id', $country);
    }

    private function supplierByState($country, $state, $query) {
        $query
        ->where('country_id',$country)
        ->where('state_id', $state);
    }

    private function supplierByCategory($category, $query) {
        $query->whereHas('categories', function ($query) use ($category) {
            $query->whereIn('categories.id', $category);
        });
    }
}
