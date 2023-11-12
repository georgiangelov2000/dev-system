<?php

namespace App\Repository;
use App\Helpers\FunctionsHelper;
use App\Models\InvoicePurchase;

class PurchaseInvoiceRepository implements ApiRepository
{
    public function getData($request){

        $invoiceQ = InvoicePurchase::query();
        $select_json = $request->input('select_json');

        $this->applyFilters($request, $invoiceQ);

        if(boolval($select_json)) {
            return $this->applySelectFieldJSON($invoiceQ);
        }
    }

    private function applyFilters($request, $query){
        $query->when($request->input('invoice'), function ($query) use ($request) {
            return $query->where('id', $request->input('invoice'));
        });
    }

    private function applySelectFieldJSON($query){
        return response()->json($query->first());
    }

    public function helper() {
        return new FunctionsHelper();
    }
}
