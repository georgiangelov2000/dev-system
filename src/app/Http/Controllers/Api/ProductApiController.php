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
        $publishing = $request->publishing;
        $single_total_price = $request->single_total_price;
        $total_price_range = $request->total_price_range;
        $search = $request->search;
        $out_of_stock = $request->out_of_stock;
        
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $productQuery = $this->buildProductQuery();

        if ($search) {
            $productQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        if ($supplier) {
            $productQuery->where('supplier_id', $supplier);
        }
        if ($category) {
            $productQuery->whereHas('categories', function ($query) use ($category) {
                $query->where('category_id', $category);
            });
        }
        if ($sub_category) {
            $productQuery->whereHas('subcategories', function ($query) use ($sub_category) {
                $query->whereIn('subcategories.id', $sub_category);
            });
        }
        if ($brand) {
            $productQuery->whereHas('brands', function ($query) use ($brand) {
                $query->whereIn('brands.id', $brand);
            });
        }
        if ($publishing) {
            $dates = explode(" - ", $publishing);
            $date1_formatted = date('Y-m-d 23:59:59', strtotime($dates[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($dates[1]));

            $productQuery
            ->where('created_at', '>=', $date1_formatted)
            ->where('created_at', '<=', $date2_formatted);
        }
        if ($total_price_range) {
            $pieces = explode('-', $total_price_range);

            $productQuery
            ->where('total_price', '>=', (int)$pieces[0])
            ->where('total_price', '<=', (int)$pieces[1]);
        }

        if ($single_total_price) {
            $productQuery->where('total_price', 'LIKE', '%' . $single_total_price . '%');
        }
        if ($out_of_stock) {
            $productQuery->where('quantity', '>', 0)->where('status', 'enabled');
        } else {
            $productQuery->where('quantity', '<=', 0)->where('status', 'disabled');
        }


        $filteredRecords = $productQuery->count();
        $result = $productQuery->skip($offset)->take($limit)->get();
        $totalRecords = Product::count();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    private function buildProductQuery()
    {
        return Product::query()->with(['categories', 'subcategories', 'brands', 'images', 'supplier:id,name'])
            ->select(
                'id',
                'name',
                'supplier_id',
                'quantity',
                'notes',
                'price',
                'total_price',
                'code',
                'status',
                'created_at',
                'is_paid',
                'initial_quantity'
            )
            ->orderBy('id', 'desc');
    }
}
