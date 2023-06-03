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

        if ($supplier) {
            $productQuery->where('supplier_id', $supplier);
        }
        if ($category) {
            $this->fillterByCategories($productQuery, $category);
        }
        if ($sub_category) {
            $this->fillterBySubCategories($productQuery,$sub_category);
        }
        if ($brand) {
            $productQuery->whereHas('brands', function ($query) use ($brand) {
                $query->whereIn('brands.id', $brand);
            });
        } 
        if ($publishing) {
            $date_pieces = explode(' - ',$publishing);

            $start_date = new DateTime($date_pieces[0]);
            $end_date = new DateTime($date_pieces[1]);
            
            $this->fillterByCreatedAt(
                $productQuery,
                $start_date->format('Y-m-d H:i:s'),
                $end_date->format('Y-m-d H:i:s')
            );
        }
        if ($total_price_range) {
            $pieces = explode('-',$total_price_range);
            $this->fillterTotalPrice(
                $productQuery,
                $pieces[0],
                $pieces[1]
            );
        }
        if ($single_total_price) {
            $productQuery->where('total_price', 'LIKE', '%'.$single_total_price.'%');
        }        
        if($search) {
            $productQuery->where('name', 'LIKE', '%'.$search.'%');
            if($out_of_stock) {
                $productQuery->where('quantity','>',0);
            }
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
            ->where('status', 'enabled')
            ->where('initial_quantity', '>', 0)
            ->where('quantity', '>', 0)
            ->orderBy('id', 'desc');
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

    private function fillterTotalPrice($query, $total_price_start, $end_price_start) {   
        return $query->whereBetween('total_price', [
            floatval($total_price_start),
            floatval($end_price_start)
        ]);
    }
}
