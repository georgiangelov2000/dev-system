<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceOrderRequest;
use App\Http\Requests\InvoicePurchaseRequest;
use App\Models\InvoicePurchase;
use App\Models\InvoiceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    
    public function updatePurchaseInvoice(InvoicePurchase $invoice, InvoicePurchaseRequest $request) {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            $invoice->update($data);

            DB::commit();
            return response()->json(['message' => 'Successfully updated invoice',200]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Unsuccesfully updated invoice',500]);
        }
    }

    public function updateOrderInvoice(InvoiceOrder $invoice, InvoiceOrderRequest $request) {
        DB::beginTransaction();
        try {
            
            $data = $request->validated();

            $invoice->update($data);
            
            DB::commit();
            return response()->json(['message' => 'Successfully updated invoice',200]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Unsuccesfully updated invoice',500]);
        }
    }
}
