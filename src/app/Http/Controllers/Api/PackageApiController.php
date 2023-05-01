<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageApiController extends Controller
{
    public function getData(Request $request) {
        $packageQuery = $this->buildPackageQuery();
        $package = $request->package;
        $delievery = $request->delievery;
        $customer = $request->customer;

        if($package) {
            $packageQuery->where('package_type',$package);
        }
        if($delievery) {
            $packageQuery->where('delievery_method',$delievery);
        }
        if($customer) {
            $packageQuery->whereHas('orders', function ($query) use ($customer) {
                $query->where('customer_id', $customer);
            });
        }

        $result = $this->getPackages($packageQuery);

        foreach ($result as $key => $package) {
            $package->package_type = array_key_exists($package->package_type, config('statuses.package_types')) ? config('statuses.package_types.' . $package->package_type) : $package->package_type;
            $package->delievery_method = array_key_exists($package->delievery_method, config('statuses.delievery_methods')) ? config('statuses.delievery_methods.' . $package->delievery_method) : $package->delievery_method;

        }
        
        return response()->json(['data' => $result],200);
    }

    private function buildPackageQuery(){
        return Package::query()->select(
            'id',
            'package_name',
            'tracking_number',
            'package_type',
            'delievery_method',
            'package_price',
            'delievery_date',
            'package_notes',
            'customer_notes'
        )->withCount('orders');
    }

    private function getPackages($query)
    {
        return $query->get();
    }
}
