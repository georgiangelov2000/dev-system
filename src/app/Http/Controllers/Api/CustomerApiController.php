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
                $this->customerByState($state, $customerQuery);
            }
        }
        if($search) {
            $customerQuery->where('name', 'LIKE', '%'.$search.'%');
        }

        $result = $this->getCustomers($customerQuery);

        return response()->json(['data' => $result]);
    }

    private function buildCustomerQuery(){
        return Customer::query()->select(
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
        )
        ->with('state','country','image');
    }

    private function customerByCountry($country, $query) {
        $query->where('country_id', $country);
    }

    private function customerByState($state, $query) {
        $query->where('state_id', $state);
    }

    private function getCustomers($query) {
        return $query->get();
    }
}
