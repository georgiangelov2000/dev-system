<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use DateTime;

class ProductApiController extends Controller
{

    public function getData(Request $request)
    {
        $supplier = $request->supplier;
        $category = $request->category;
        $sub_category = $request->sub_category;
        $brand = $request->brand;
        $start_date = $request->start_date ? new DateTime($request->start_date) : false;
        $end_date = $request->end_date ? new DateTime($request->end_date) : false;
        $start_total_price = $request->start_total_price;
        $end_total_price = $request->end_total_price;
        $single_total_price = $request->single_total_price;
        $search = $request->search;

        $productQuery = $this->buildProductQuery();

        if ($supplier) {
            $productQuery->where('supplier_id', $supplier);
        }
        if ($category) {
            $this->fillterByCategories($productQuery, $category);

            if ($sub_category) {
                $this->fillterBySubCategories($productQuery,$category);
            }
        }
        if ($brand) {
            $productQuery->whereHas('brands', function ($query) use ($brand) {
                $query->whereIn('brands.id', $brand);
            });
        }
        if ($start_date && $end_date) {
            $this->fillterByCreatedAt(
                $productQuery,
                $start_date->format('Y-m-d H:i:s'),
                $end_date->format('Y-m-d H:i:s')
            );
        }
        if ($start_total_price && $end_total_price) {
            $this->fillterTotalPrice(
                $productQuery,
                floatval($start_total_price),
                floatval($end_total_price)
            );
        }
        if ($single_total_price) {
            $productQuery->where('total_price', floatval($single_total_price));
        }
        if($search) {
            $productQuery->where('name', 'LIKE', '%'.$search.'%');
        }

        $result = $this->getProducts($productQuery);

        return response()->json(['data' => $result]);
    }

    private function buildProductQuery()
    {
        return Product::query()->with(['categories', 'subcategories', 'brands', 'images', 'supplier:id,name'])
            ->select('id', 'name', 'supplier_id', 'quantity', 'notes', 'price', 'total_price', 'code', 'status', 'created_at','is_paid','initial_quantity')
            ->where('status', 'enabled')
            ->where('initial_quantity', '>', 0)
            ->orderBy('id', 'asc');
    }

    private function fillterByCategories($query, $category)
    {
        $query->whereHas('categories', function ($query) use ($category) {
            $query->where('category_id', $category);
        });
    }

    private function fillterBySubCategories($query, $sub_category) {
        $query->whereHas('subcategories', function ($query) use ($sub_category) {
            $query->whereIn('subcategories.id', $sub_category);
        });
    }

    private function fillterByCreatedAt($query, $start_date, $end_date)
    {
        return $query->whereBetween('created_at', [
            $start_date,
            $end_date
        ]);
    }

    private function fillterTotalPrice($query,$start_price,$end_price) {
        return $query->whereBetween('total_price', [
            floatval($start_price),
            floatval($end_price)
        ]);
    }

    private function getProducts($query)
    {
        return $query->get();
    }
}
