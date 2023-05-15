<?php

namespace App\Helpers;

use App\Models\SubCategory;
use App\Models\State;
use App\Models\Country;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Customer;

class LoadStaticData
{

    public static function callSubCategories($option = null)
    {
        $subCategoryQ = SubCategory::select('id', 'name');
    
        if ($option === 'assigned') {
            $subCategoryQ->has('category');
        } else if ($option === 'unassigned') {
            $subCategoryQ->doesntHave('category');
        }
    
        $result = $subCategoryQ->get();
    
        return $result;
    }

    static function callStatesAndCountries($country = null, $option = null)
    {

        if ($country && $option === 'states') {
           return State::select('id', 'name')->where('country_id', $country)->get();
        }
        else if($option === 'states') {
           return State::select('id', 'name')->get();
        }
        else {
            return Country::select('id', 'name')->get();
        };
    }

    static function loadCallCategories($option = null)
    {
        $query = Category::select('id', 'name');

        if ($option == 'unnasigned') {
            $query->whereDoesntHave('suppliers');
        }

        return $query->get();
    }

    static function callSupliers()
    {
        $query = Supplier::select('id','name')
        ->whereNotNull('country_id')
        ->whereNotNull('state_id')
        ->whereHas('categories')
        ->get();

        return $query;
    }

    static function callBrands()
    {
        $query = Brand::query();
        $brands = $query->select('id', 'name')
            ->get();

        return $brands;
    }

    static function callCustomers()
    {
        $query = Customer::query();

        $customers = $query->select('id', 'name')->get();

        return $customers;
    }
}
