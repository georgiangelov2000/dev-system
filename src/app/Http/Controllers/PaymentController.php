<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Requests\OrderPaymentRequest;
use App\Http\Requests\PurchasePaymentRequest;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\InvoiceOrder;
use App\Models\InvoicePurchase;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Models\CompanySettings;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPurchasePayment()
    {
        $suppliers = Supplier::has('purchases')->select('id', 'name')->get();

        return view('purchases.create_payment', [
            'suppliers' => $suppliers
        ]);
    }



    public function supplierPayments()
    {
        $suppliers = Supplier::has('purchases')->select('id', 'name')->get();

        return view('payments.purchase_payments', [
            'suppliers' => $suppliers
        ]);
    }

    public function customerPayments()
    {
        $customers = Customer::has('orders')->select('id', 'name')->get();
        return view('payments.order_payments', ['customers' => $customers]);
    }

    public function editPurchasePayment(PurchasePayment $payment)
    {
        $payment->load('purchase.supplier', 'purchase.categories', 'invoice');
        $company = CompanySettings::select('id', 'name', 'phone_number', 'address', 'tax_number', 'image_path')->first();
        return view(
            'purchases.edit_payment',
            [
                'payment' => $payment,
                'company' => $company,
            ]
        );
    }

    public function storePurchasePayment(PurchasePaymentRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (isset($data['purchase_id']) && count($data['purchase_id'])) {
                $purchases = $data['purchase_id'];

                foreach ($purchases as $key => $id) {
                    $purchase = Purchase::find($id);

                    if ($purchase) {
                        $purchase->status = 2;
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
            return response()->json(['message' => 'Payment has not been created'], 500);
        }
    }

    public function updatePurchasePayment(PurchasePayment $payment, PurchasePaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            
            // Update the payment status and order status
            $paymentStatus = (int) $data['payment_status'];
            $paymentDate = strtotime($data['date_of_payment']);
            $dateOfPayment = strtotime($payment->purchase->expected_date_of_payment);

            // Check if payment date is greater than sale date (Overdue payment)
            if ($paymentDate > $dateOfPayment) {
                $payment->purchase->is_paid = 1; // Mark as paid
                $payment->purchase->status = 4;  // Mark as overdue
                $data['payment_status'] = 4; // Update the payment status to 'Overdue'
            } else {
                // Check the regular payment status values
                if ($paymentStatus === 1 || $paymentStatus === 4) {
                    $payment->purchase->is_paid = 1; // Mark as paid
                    $payment->purchase->status = $paymentStatus;
                } elseif ($paymentStatus === 2) {
                    $payment->purchase->is_paid = 0; // Mark as not paid
                    $payment->purchase->status = $paymentStatus;
                } elseif ($paymentStatus === 5) {
                    $payment->purchase->is_paid = 2; // Mark with custom status
                    $payment->purchase->status = $paymentStatus;
                } elseif ($paymentStatus === 3) {
                    $payment->purchase->is_paid = 3; // Mark with custom status
                    $payment->purchase->status = $paymentStatus;
                }
            }
            
            $payment->purchase->save();

            $payment->update($data);

            $invoice = $payment->invoice ?: new InvoicePurchase();
            $invoice->purchase_payment_id = $payment->id;
            $invoice->price = $payment->purchase->total_price;
            $invoice->quantity = $payment->purchase->initial_quantity;
            $invoice->save();

            DB::commit();
            return redirect()->back()->with('success', 'Payment has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Payment has not been updated');
        }
    }


    // Order payments
    public function createOrderPayment()
    {
        $customers = Customer::has('orders')->select('id', 'name')->get();
        return view('orders.create_payment', ['customers' => $customers]);
    }

    public function editOrderPayment(OrderPayment $payment)
    {
        $payment->load('order.customer', 'invoice');
        $company = CompanySettings::select('id', 'name', 'phone_number', 'address', 'tax_number', 'image_path')->first();
        return view(
            'orders.edit_payment',
            [
                'payment' => $payment,
                'company' => $company,
            ]
        );
    }

    public function storeOrderPayment(OrderPaymentRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (isset($data['order_id']) && count($data['order_id'])) {
                $orders = $data['order_id'];

                foreach ($orders as $key => $id) {
                    $order = Order::find($id);

                    if ($order) {
                        $status = 2;

                        $paymentDate = date('Y-m-d', strtotime($data['date_of_payment'][$key]));
                        $saleDate = strtotime($order->date_of_sale);

                        if ($paymentDate > $saleDate) {
                            $status = 4;
                        }

                        $order->status = $status;
                        $order->save();

                        $paymentData = [
                            'order_id' => $id,
                            'price' => $data['price'][$key],
                            'quantity' => $data['quantity'][$key],
                            $paymentDate
                        ];

                        $orderInvoice = OrderPayment::create($paymentData);

                        $orderInvoice->invoice()->create([
                            'price' => $order->total_sold_price,
                            'quantity' => $order->sold_quantity
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Payment has been created'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Payment has not been created'], 500);
        }
    }

    public function updateOrderPayment(OrderPayment $payment, OrderPaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Update the payment status and order status
            $paymentStatus = (int) $data['payment_status'];

            $paymentDate = strtotime($data['date_of_payment']);
            $saleDate = strtotime($payment->order->date_of_sale);

            // Check if payment date is greater than sale date (Overdue payment)
            if ($paymentDate > $saleDate) {
                $payment->order->is_paid = 1; // Mark as paid
                $payment->order->status = 4;  // Mark as overdue
                $data['payment_status'] = 4; // Update the payment status to 'Overdue'
            } else {
                // Check the regular payment status values
                if (in_array($paymentStatus, [1, 4])) {
                    $payment->order->is_paid = 1; // Mark as paid
                    $payment->order->status = $paymentStatus;
                } elseif ($paymentStatus === 2) {
                    $payment->order->is_paid = 0; // Mark as not paid
                    $payment->order->status = $paymentStatus;
                } elseif ($paymentStatus === 5) {
                    $payment->order->is_paid = 2; // Mark with custom status
                    $payment->order->status = $paymentStatus;
                } elseif ($paymentStatus === 3) {
                    $payment->order->is_paid = 3; // Mark with custom status
                    $payment->order->status = $paymentStatus;
                }
            }

            $payment->order->save();

            $payment->update($data);

            $invoice = $payment->invoice ?: new InvoiceOrder();
            $invoice->order_payment_id = $payment->id;
            $invoice->price = $payment->order->total_sold_price;
            $invoice->quantity = $payment->order->sold_quantity;
            $invoice->save();

            DB::commit();
            return redirect()->back()->with('success', 'Payment has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Payment has not been updated');
        }
    }
}
