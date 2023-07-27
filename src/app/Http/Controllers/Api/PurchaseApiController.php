<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;

class PurchaseApiController extends Controller
{

    public function getData(Request $request)
    {

        $supplier = isset($request->supplier) ? $request->supplier : null;
        $category = isset($request->category) ? $request->category : null;
        $status = isset($request->status) ? $request->status : null;
        $sub_category = isset($request->sub_category) ? $request->sub_category : null;
        $brand = isset($request->brand) ? $request->brand : null;
        $single_total_price = isset($request->single_total_price) ? $request->single_total_price : null;
        $total_price_range = isset($request->total_price_range) ? $request->total_price_range : null;
        $search = isset($request->search) ? $request->search : null;
        $select_json = isset($request->select_json) && $request->select_json ? $request->select_json : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $limit  = isset($request->limit) ? $request->limit : null;
        $is_paid = isset($request->is_paid) && !$request->is_paid ? false : true;
        $offset = $request->input('start', 0);

        $purchaseQuery = Purchase::query()->select(
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
            'initial_quantity',
            'expected_date_of_payment',
            'original_price',
            'discount_percent'
        );

        if($limit) {
            $purchaseQuery->skip($offset)->take($limit);
        }
        if ($column_name && $order_dir) {
            $purchaseQuery->orderBy($column_name, $order_dir);
        }
        if ($search) {
            $purchaseQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        if($status) {
            $purchaseQuery->whereIn('status', $status);
        }
        if ($supplier) {
            $purchaseQuery->where('supplier_id', $supplier);
        }
        if ($category) {
            $purchaseQuery->whereHas('categories', function ($query) use ($category) {
                $query->where('category_id', $category);
            });
        }
        if ($sub_category) {
            $purchaseQuery->whereHas('subcategories', function ($query) use ($sub_category) {
                $query->whereIn('subcategories.id', $sub_category);
            });
        }
        if ($brand) {
            if(is_array($brand)) {
                $purchaseQuery->whereHas('brands', function ($query) use ($brand) {
                    $query->whereIn('brands.id', $brand);
                });
            } else {
                $purchaseQuery->whereHas('brands', function ($query) use ($brand) {
                    $query->where('brands.id', $brand);
                });
            }
        }
        if ($total_price_range) {
            $pieces = explode('-', $total_price_range);

            $purchaseQuery
                ->where('total_price', '>=', (int)$pieces[0])
                ->where('total_price', '<=', (int)$pieces[1]);
        }

        if ($single_total_price) {
            $purchaseQuery->where('total_price', 'LIKE', '%' . $single_total_price . '%');
        }
        if(!$is_paid) {
            $purchaseQuery->where('is_paid',0);
        }
        if (isset($request->out_of_stock)) {
            if($request->out_of_stock) {
                $purchaseQuery->where('quantity', '>', 0);
            } else {
                $purchaseQuery->where('quantity', '<=', 0);
            }
        }
        if ($select_json !== null) {
            $purchaseQuery->with(['categories:id,name','brands']);
            return response()->json(
                $purchaseQuery->get()
            );
        } else {
            $purchaseQuery->with(['categories', 'subcategories', 'brands', 'images', 'supplier:id,name', 'orders:id,status,is_paid','payment:id,purchase_id,date_of_payment']);
            
            $purchaseQuery
                ->withCount([
                    'orders as paid_orders_count' => function ($query) {
                        $query->where('status', 1)->where('is_paid', 1);
                    },
                    'orders as unpaid_orders_count' => function ($query) {
                        $query->whereIn('status', [2,3,4,5,6])->where('is_paid', false);
                    }
                ]);
        }

        $filteredRecords = $purchaseQuery->count();
        $result = $purchaseQuery->get();
        $totalRecords = Purchase::count();

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
