<?php

namespace App\Repository\API;
use App\Helpers\FunctionsHelper;
use App\Models\PurchasePayment;
use App\Models\Supplier;

class PurchasePaymentRepository implements ApiRepository
{
    public $supplier;
    public $date; 

    public function getData($request)
    {
        $relations = [
            'purchase:id,name,supplier_id,quantity,price,total_price,initial_quantity,notes,code,image_path,discount_percent,expected_delivery_date,delivery_date',
            'purchase.categories',
            'invoice'
        ];

        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $paymentQ = PurchasePayment::query();
        $this->applyFilters($request,$paymentQ);

        $paymentQ->with($relations);
        
        $filteredRecords = $paymentQ->count();
        $totalRecords = PurchasePayment::count();
        $sum = number_format($paymentQ->sum('price'), 2, '.', '');
        $result = $paymentQ->skip($offset)->take($limit)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'supplier' => $this->supplier,
            'sum' => $sum,
            'date' => $this->date,
            'data' => $result
        ]);
    }

    private function applyFilters($request, $query){
        $date = $request->input('date',null);
        list($dateStart, $dateEnd) = $this->helper()->dateRange($date);
        $this->date  =$this->helper()->dateToString($dateStart, $dateEnd);

        $query->when($request->input('user'), function ($query) use ($request) {
            $this->supplierData($request->input('user'));
            return $query->whereHas('purchase', fn ($query) => $query->where('supplier_id', $request->input('user')));
        });

        $query->when($request->input('delivery_status'), function ($query) use ($request) {
            return $query->where('delivery_status', '=', $request->input('delivery_status'));
        });

        $query->when($request->input('payment_status'), function ($query) use ($request) {
            return $query->where('payment_status', '=', $request->input('payment_status'));
        });

        $query->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            return $query
                ->where('date_of_payment', '>=', $dateStart)
                ->where('date_of_payment', '<=', $dateEnd);
        });

        return $query;
    }

    private function supplierData($id){
        $this->supplier = Supplier::with(['state:id,name', 'country:id,name,short_name'])->find($id);
    }

    public function helper() {
        return new FunctionsHelper();
    }
}
