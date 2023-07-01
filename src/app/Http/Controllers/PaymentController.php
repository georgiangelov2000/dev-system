<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Http\Requests\PurchasePaymentRequest;
use App\Models\InvoicePurchase;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createSupplierPayment()
    {
        $suppliers = Supplier::has('purchases')->select('id', 'name')->get();

        return view('purchases.create_payment', [
            'suppliers' => $suppliers
        ]);
    }

    public function supplierPayments()
    {
        $suppliers = Supplier::has('purchases')->select('id', 'name')->get();

        return view('payments.supplier_payments', [
            'suppliers' => $suppliers
        ]);
    }

    public function editSupplierPayment(PurchasePayment $payment)
    {
        $payment->load('purchase.supplier');
        return view('payments.edit_supplier_payment', compact('payment'));
    }

    public function storeSupplierPayment(PurchasePaymentRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (isset($data['purchase_id']) && count($data['purchase_id'])) {
                $purchases = $data['purchase_id'];

                foreach ($purchases as $key => $id) {
                    $purchase = Purchase::find($id);

                    if ($purchase) {
                        $purchase->is_paid = 1;
                        $purchase->save();

                        $paymentData = [
                            'purchase_id' => $id,
                            'price' => $data['price'][$key],
                            'quantity' => $data['quantity'][$key],
                            'date_of_payment' => date('Y-m-d', strtotime($data['date_of_payment'][$key]))
                        ];

                        $supplierPaymentRecord = PurchasePayment::create($paymentData);

                        $supplierPaymentRecord->invoice()->create([
                            'price' => $purchase->total_price,
                            'quantity' => $purchase->initial_quantity
                        ]);                        

                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Payment has been created'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Payment has not been created'], 200);
        }
    }
}
