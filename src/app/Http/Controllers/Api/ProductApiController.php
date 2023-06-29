<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller
{

    public function getData(Request $request)
    {

        $supplier = isset($request->supplier) ? $request->supplier : null;
        $category = isset($request->category) ? $request->category : null;
        $sub_category = isset($request->sub_category) ? $request->sub_category : null;
        $brand = isset($request->brand) ? $request->brand : null;
        $publishing = isset($request->publishing) ? $request->publishing : null;
        $single_total_price = isset($request->single_total_price) ? $request->single_total_price : null;
        $total_price_range = isset($request->total_price_range) ? $request->total_price_range : null;
        $search = isset($request->search) ? $request->search : null;
        $out_of_stock = isset($request->out_of_stock) ? $request->out_of_stock : null;
        $select_json = isset($request->select_json) && $request->select_json ? $request->select_json : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $limit  = isset($request->limit) ? $request->limit : null;
        
        $offset = $request->input('start', 0);

        $productQuery = Product::query()->select(
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
        );
        if($limit) {
            $productQuery->skip($offset)->take($limit);
        }
        if ($column_name && $order_dir) {
            $productQuery->orderBy($column_name, $order_dir);
        }
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
            if(is_array($brand)) {
                $productQuery->whereHas('brands', function ($query) use ($brand) {
                    $query->whereIn('brands.id', $brand);
                });
            } else {
                $productQuery->whereHas('brands', function ($query) use ($brand) {
                    $query->where('brands.id', $brand);
                });
            }
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
        if(isset($request->is_paid) && $request->is_paid == false) {
            $productQuery->where('is_paid',0)->whereDoesntHave('payments');
        }
        if ($out_of_stock) {
            $productQuery->where('quantity', '>', 0)->where('status', 'enabled');
        } else {
            $productQuery->where('quantity', '<=', 0)->where('status', 'disabled');
        }

        if ($select_json !== null) {
            $productQuery->with([]);
            return response()->json(
                $productQuery->get()
            );
        } else {
            $productQuery->with(['categories', 'subcategories', 'brands', 'images', 'supplier:id,name', "orders:id,status,is_paid"]);

            $productQuery
                ->withCount([
                    'orders as paid_orders_count' => function ($query) {
                        $query->where('status', 1)->where('is_paid', 1);
                    },
                    'orders as unpaid_orders_count' => function ($query) {
                        $query->whereIn('status', [3, 4])->where('is_paid', false);
                    }
                ]);
        }

        $filteredRecords = $productQuery->count();
        $result = $productQuery->get();
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
}
