<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;

class PurchaseApiController extends Controller
{

    public function getData(Request $request)
    {
        $relations = ['categories', 'subcategories', 'brands', 'supplier:id,name'];

        $supplier = isset($request->supplier) ? $request->supplier : null;
        $id = isset($request->id) ? $request->id : null;
        $category = isset($request->category) ? $request->category : null;
        $status = isset($request->status) ? $request->status : null;
        $sub_category = isset($request->sub_category) ? $request->sub_category : null;
        $brand = isset($request->brand) ? $request->brand : null;
        $single_total_price = isset($request->single_total_price) ? $request->single_total_price : null;
        $total_price_range = isset($request->total_price_range) ? $request->total_price_range : null;
        $search = isset($request->search) ? $request->search : null;
        $select_json = isset($request->select_json) ? boolval($request->select_json) : null;
        $order_dir = isset($request->order_dir) ? $request->order_dir : null;
        $column_name = isset($request->order_column) ? $request->order_column : null;
        $limit  = isset($request->limit) ? $request->limit : null;
        $out_of_stock = isset($request->out_of_stock) ? $request->out_of_stock : null;
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
            'initial_quantity',
            'expected_date_of_payment',
            'original_price',
            'discount_percent',
            'discount_price',
            'image_path',
        );
        
        if ($limit) {
            $purchaseQuery->skip($offset)->take($limit);
        }
        if ($column_name && $order_dir) {
            $purchaseQuery->orderBy($column_name, $order_dir);
        }
        if ($search) {
            $purchaseQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        if ($id) {
            $purchaseQuery->where('id', $id);
        }
        if ($status) {
            $purchaseQuery->whereHas('payment',function($query)use($status){
                $query->whereIn('purchase_payments.payment_status',$status);
            });
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
            if (is_array($brand)) {
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
        if (!is_null($out_of_stock)) {
            if(boolval($out_of_stock) === true) {
                $purchaseQuery->where('quantity', '<=', 0);
            } else if(boolval($out_of_stock) === false) {
                $purchaseQuery->where('quantity', '>', 0);
            } 
        } 
         if ($select_json) {
            $purchaseQuery->with($relations);
            return response()->json(
                $purchaseQuery->get()
            );
        }

        $relations = [...$relations, ...['payment:id,payment_status,purchase_id,alias']];
        $purchaseQuery->with($relations)->withCount([
            'orders',
            // Add other relationships you want to count here
        ]);
        
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
