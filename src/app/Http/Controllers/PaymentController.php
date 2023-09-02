<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Requests\PaymentRequest;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\InvoiceOrder;
use App\Models\InvoicePurchase;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class PaymentController extends Controller
{
    public function index($type)
    {
        return $this->getView(null, $type);
    }

    public function create($type)
    {
        return $this->getView(null, $type, 'create');
    }

    public function edit(string $payment, string $type)
    {
        return $this->getView($payment, $type, 'edit');
    }

    public function store(PaymentRequest $request, $type)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            if (isset($data['id']) && count($data['id'])) {
                foreach ($data['id'] as $key => $id) {
                    $this->processPayment(
                        $request->method(),
                        $type,
                        $id,
                        $data['price'][$key],
                        $data['quantity'][$key],
                        $data['date_of_payment'][$key]
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Payment has not been created'], 500);
        }
        return response()->json(['message' => 'Payment has been created'], 200);
    }

    public function update(PaymentRequest $request, $payment, $type)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->processPayment(
                $request->method(),
                $type,
                $payment,
                $data['price'],
                $data['quantity'],
                $data['date_of_payment'],
                $data['payment_method'],
                $data['payment_reference'],
                $data['payment_status'],
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Payment has not been updated');
        }

        return redirect()->back()->with('success', 'Payment has been updated');
    }

    // Private methods
    private function getView(?string $payment = null, string $type, ?string $viewType = null): View
    {
        $data = $this->getData($payment, $type);
        $view = $viewType !== null ? view($type . 's.' . $viewType . '_payment', $data) : view('payments.' . $type . '_payments', $data);
        return $view;
    }

    private function getData(?string $payment = null, string $type): array
    {
        $relations = [];
        $data = [];

        if ($type === 'order') {
            $payment !== null  ? array_push($relations, 'order.customer', 'invoice') : [];
            $query = $payment !== null ? OrderPayment::findOrFail($payment) : Customer::has('orders')->select('id', 'name');
        } elseif ($type === 'purchase') {
            $payment !== null ? array_push($relations, 'purchase.supplier', 'invoice') : [];
            $query = $payment !== null ? PurchasePayment::findOrFail($payment) : Supplier::has('purchases')->select('id', 'name');
        }

        if ($payment !== null) {
            $jsonEncoded = Settings::where('type', 1)->first();
            $jsonDecoded = json_decode($jsonEncoded->settings, true);
            $data['settings'] = $jsonDecoded;
            $data['payment'] = $query->with($relations)->first();
        } else {
            $data[$type === 'order' ? 'customers' : 'suppliers'] = $query->get();
        }

        return $data;
    }

    private function processPayment(
        string $method,
        string $type,
        string $id,
        string $price,
        string $quantity,
        string $date_of_payment,
        ?string $payment_method = null,
        ?string $payment_reference = null,
        ?string $payment_status = null
    ) {
        $relationName = ($type === 'order') ? 'order' : 'purchase';
        $modelFounder = $this->getModelFounder($type, $id, $method);


        if ($method !== 'PUT') {
            $newRelation = $this->getNewModelRelation($type);
        }

        if ($modelFounder instanceof OrderPayment || $modelFounder instanceof PurchasePayment) {
            $relation = $modelFounder->$relationName;
            $relation->status = $payment_status;
            $relation->save();

            $modelFounder->price = $price;
            $modelFounder->quantity = $quantity;
            $modelFounder->date_of_payment = date('Y-m-d', strtotime($date_of_payment));
            $modelFounder->payment_method = $payment_method;
            $modelFounder->payment_reference = $payment_reference;
            $modelFounder->payment_status = $payment_status;

            $modelFounder->save();

            $modelFounder->invoice()->update([
                'price' => $modelFounder->price,
                'quantity' => $modelFounder->quantity
            ]);
            
        } else {
            $modelFounder->status = 2;
            $modelFounder->save();

            if ($newRelation instanceof OrderPayment) {
                $newRelation->order_id = $id;
            } elseif ($newRelation instanceof PurchasePayment) {
                $newRelation->purchase_id = $id;
            }

            $newRelation->price = $price;
            $newRelation->quantity = $quantity;
            $newRelation->date_of_payment = date('Y-m-d', strtotime($date_of_payment));

            $newRelation->save();

            $newRelation->invoice()->create([
                'price' => $newRelation->price,
                'quantity' => $newRelation->quantity
            ]);
        }
    }

    private function getModelFounder(string $type, string $id, ?string $method)
    {
        if ($type === 'order') {
            return ($method === 'PUT') ? OrderPayment::findOrFail($id) : Order::findOrFail($id);
        } elseif ($type === 'purchase') {
            return ($method === 'PUT') ? PurchasePayment::findOrFail($id) : Purchase::findOrFail($id);
        }

        return null;
    }

    private function getNewModelRelation(string $type)
    {
        if ($type === 'order') {
            return  new OrderPayment();
        } elseif ($type === 'purchase') {
            return  new PurchasePayment();
        }
    }
}
