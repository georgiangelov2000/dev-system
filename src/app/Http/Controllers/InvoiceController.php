<?php

namespace App\Http\Controllers;

use App\Models\InvoicePurchase;
use App\Models\InvoiceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function editPurchaseInvoice(InvoicePurchase $invoice){
        $invoice->load('purchasePayment.purchase:id,name,supplier_id', 'purchasePayment.purchase.supplier');
        return view('invoices.edit',compact('invoice'));
    }
    
    public function updatePurchaseInvoice(InvoicePurchase $invoice, Request $request) {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $invoice->update($data);

            DB::commit();
            return redirect()->back()->with('success', 'Invoice has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Invoice has not been updated');
        }
    }

    public function editOrderInvoice(InvoiceOrder $invoice){
        $invoice->load('purchasePayment.purchase:id,name,supplier_id', 'purchasePayment.purchase.supplier');
        return view('invoices.edit',compact('invoice'));
    }
}
