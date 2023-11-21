<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;

class PurchaseApiController extends Controller
{

    public function getData(Request $request)
    {
        $relations = ['categories','subcategories', 'brands', 'supplier:id,name','payment'];
        $select_json = $request->input('select_json');
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

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
            'is_it_delivered',
            'delivery_date',
            'expected_delivery_date',
            'original_price',
            'discount_percent',
            'discount_price',
            'image_path',
        );

        $this->applyFilters($request, $purchaseQuery);

        if ($select_json) {
            return $this->applySelectFieldJSON($purchaseQuery);
        }

        $purchaseQuery->with($relations)->withCount(['orders']);
        
        $filteredRecords = $purchaseQuery->count();
        $totalRecords = Purchase::count();
        $result = $purchaseQuery->skip($offset)->take($limit)->get();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }

    private function applyFilters($request, $query)
    {
        $query->when($request->input('order_column') && $request->input('order_dir'), function ($query) use ($request) {
            return $query->orderBy($request->input('order_column'), $request->input('order_dir'));
        });

        $query->when($request->input('search'), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        });

        $query->when($request->input('id'), function ($query) use ($request) {
            return $query->where('id', $request->id);
        });

        $query->when($request->input('status'), function ($query) use ($request) {
            $statuses = $request->input('status');
            return $query->whereHas('payment', fn ($query) => $query->whereIn('purchase_payments.payment_status', $statuses));
        });

        $query->when($request->input('supplier'), function ($query) use ($request) {
            return $query->where('supplier_id', $request->input('supplier'));
        });

        $query->when($request->input('category'), function ($query) use ($request) {
            return $query->whereHas('categories', fn ($query) => $query->where('category_id', $request->input('category')));
        });

        $query->when($request->input('sub_category'), function ($query) use ($request) {
            $sub_categories = $request->input('sub_category');
            return $query->whereHas('subcategories', fn ($query) => $query->whereIn('subcategories.id', $sub_categories));
        });

        $query->when($request->input('brand'), function ($query) use ($request) {
            $brands = $request->input('brand');
            return $query->whereHas('brands', fn ($query) => $query->whereIn('brands.id', $brands));
        });

        $query->when($request->input('total_price_range'), function ($query) use ($request) {
            [$minPrice, $maxPrice] = array_map('intval', explode('-', $request->input('total_price_range')));
            return $query->whereBetween('total_price', [$minPrice, $maxPrice]);
        });

        $query->when($request->input('single_total_price'), function ($query) use ($request) {
            return $query->where('total_price', 'LIKE', '%' . $request->input('single_total_price') . '%');
        });

        $query->when($request->input('out_of_stock') !== null, function ($query) use ($request) {
            $quantityComparison = boolval($request->input('out_of_stock')) ? '<=' : '>';
            return $query->where('quantity', $quantityComparison, 0);
        });
    }

    private function applySelectFieldJSON($query){
        return response()->json($query->get());
    }
}
