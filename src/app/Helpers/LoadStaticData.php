<?php

namespace App\Helpers;

use App\Models\SubCategory;
use Illuminate\Support\Facades\Cache;
use App\Models\State;
use App\Models\Country;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Customer;

class LoadStaticData {

    static function callSubCategories($option = null) {
        $subCategoryQ = SubCategory::query()->select('id','name');

        if($option === 'assigned') {
            $subCategoryQ->has('categories');
        }
        else if ($option === 'unnasigned') {
            $subCategoryQ->whereDoesntHave('categories');
        }

        $result = $subCategoryQ->get();

        return $result;
    }

    static function callStatesAndCountries($country = null) {

        $statesQuery = State::select('id', 'name');

        if ($country) {
            $statesQuery->where('country_id', $country);
        }

        $states = $statesQuery->get();
        $countries = Country::select('id', 'name')->get();

        return [
            'states' => $states,
            'countries' => $countries
        ];
    }

    static function loadCallCategories($option = null) {
        $query = Category::select('id', 'name');

        if ($option == 'unnasigned') {
            $query->whereDoesntHave('suppliers');
        } else {
            $query->get();
        }

        return $query->get();
    }

    static function callSupliers() {
        $query = Supplier::query()->with('categories');

        $suppliers = $query
                ->whereNotNull('country_id')
                ->whereNotNull('state_id')
                ->whereHas('categories')
                ->get();

        return $suppliers;
    }

    static function callBrands() {
        $query = Brand::query();
        $brands = $query->select('id', 'name')
                ->get();

        return $brands;
    }

}
