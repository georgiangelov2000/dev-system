<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerApiController extends Controller
{
    public function getData(Request $request){
        $country = $request->country;
        $state = $request->state;
        $search = $request->search;

        $customerQuery = $this->buildCustomerQuery();

        if($country) {
            $this->customerByCountry($country, $customerQuery);
            if ($state) {
                $this->customerByState($country, $state, $customerQuery);
            }
        }
        if($search) {
            $customerQuery->where('name', 'LIKE', '%'.$search.'%');
        }

        $offset = $request->input('start', 0);  
        $limit = $request->input('length', 10);
        $totalRecords = Customer::count();
        $filteredRecords = $customerQuery->count();
        $result = $customerQuery->skip($offset)->take($limit)->get();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );

    }

    private function buildCustomerQuery(){
        return Customer::query()->select(
            "id",
            "name",
            "email",
            "phone",
            "address",
            "zip",
            "website",
            "notes",
            "state_id",
            "country_id",
            "image_path"
        )
        ->with(['state:id,country_id,name','country:id,name,country_code,short_name'])
        ->withCount([
            'orders as paid_orders_count' => function($query) {
                $query->where('status',1)
                ->where('is_paid',1);
            },
            'orders as overdue_orders_count' => function($query) {
                $query->where('status',4)
                ->where('is_paid',1);
            },
            'orders as pending_orders_count' => function($query) {
                $query->where('status',2)
                ->where('is_paid',0);
            },
            'orders as refund_orders_count' => function($query) {
                $query->where('status',5)
                ->where('is_paid',2);
            }
        ]);
    }

    private function customerByCountry($country, $query) {
        $query
        ->where('country_id', $country);
    }

    private function customerByState($country, $state, $query) {
        $query
        ->where('country_id', $country)
        ->where('state_id',$state);
    }
}
