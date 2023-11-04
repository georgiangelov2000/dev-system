<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderMassEditRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Package;
use App\Models\Purchase;
use App\Services\OrderService;

class OrderController extends Controller
{
    private $service;
    private $helper;
    private $statuses;

    const INIT_STATUS = 2;

    public function __construct(OrderService $service, FunctionsHelper $helper)
    {
        $this->service = $service;
        $this->helper = $helper;
        $this->statuses = config('statuses.payment_statuses');
    }

    /**
     * Display a listing of orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('orders.index');
    }

    /**
     * Show the form for creating a new order.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            if (count($data['purchase_id'])) {
                foreach ($data['purchase_id'] as $key => $id) {
                    $this->orderProcessing($data, null, $key);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been created'], 500);
        }
        return response()->json(['message' => 'Order has been created'], 200);
    }

    public function edit(Order $order)
    {
        $order->load(
            'customer:id,name',
            'user:id,username',
            'purchase.categories',
            'purchase.brands',
            'purchase'
        );
        return view('orders.edit', compact('order'));
    }

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $this->orderProcessing($data, $order);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Order has not been updated');
        }
        return redirect()->route('order.index')->with('success', 'Order has been updated');
    }

    public function massUpdate(OrderMassEditRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (count($data['order_ids'])) {
                foreach ($data['order_ids'] as $key => $value) {
                    $order = Order::find($value);
                    $this->orderProcessing($data, $order);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Orders has not been updated', 500]);
        }
        return response()->json(['message' => 'Orders has been updated'], 200);
    }

    /**
     * Update the specified order status and detach package if needed.
     *
     * @param  \App\Models\Order  $order
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Order $order, Request $request)
    {
        try {

            $specificColumns = $request->only(['status', 'detach_package']);

            $detachPackage = isset($specificColumns['detach_package'])
                && $specificColumns['detach_package'] == true ? true : false;

            if ($detachPackage) {
                $package = $order->packages()->first();
                if ($package) {
                    $order->packages()->detach($package->id);
                    $order->package_extension_date = null;
                }
            }

            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been updated'], 500);
        }
        return response()->json(['message' => 'Order has been updated'], 200);
    }

    /**
     * Delete an order and update the product quantity.
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Order $order)
    {
        DB::beginTransaction();

        try {

            $product = $order->product;
            $product->quantity += $order->sold_quantity;

            $product->save();
            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Order has not been deleted');
        }
        return response()->json(['message' => 'Order has been deleted'], 200);
    }

    /**
     * Process order updates, including basic order information and completed order details.
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    private function orderProcessing(array $data, $order = null, $key = null)
    {
        $order = $order ? $order : new Order;
        $isNewOrder = !$order->exists; // Check if it's a new order
        $status = $isNewOrder
            ? $this->helper->statusValidation(self::INIT_STATUS, $this->statuses)
            : $this->helper->statusValidation($order->payment->payment_status, $this->statuses);

        if (($order && $status === self::INIT_STATUS) || $isNewOrder) {

            // Create order based of found purchase
            $purchase = $key !== null ? Purchase::findOrFail($data['purchase_id'][$key]) : $data['purchase_id'];

            // Update properties based on $key using null coalescing operator
            $amount = $data['sold_quantity'][$key] ?? $data['sold_quantity'];

            // Defined single price and discount
            $singlePrice = $data['single_sold_price'][$key] ?? $data['single_sold_price'];
            $discount = $data['discount_percent'][$key] ?? $data['discount_percent'];

            // Calculate total sold quantity and remaining quantity
            $totalSoldAmount = $purchase->orders->sum('sold_quantity');
            $remainingAmount = ($totalSoldAmount - $order->getOriginal('sold_quantity'));
            $updatedAmount = ($remainingAmount + $amount);

            // Check if the updated amount exceeds the initial purchase amount
            if ($updatedAmount > $purchase->initial_quantity) {
                return response()->json(['message', 'Purchase quantity is not enough']);
            }

            // Update purchase amount and prices
            $finalQuantity = (intval($purchase->initial_quantity) - intval($updatedAmount));
            $purchase->quantity = $finalQuantity;

            // Calculate prices for current order
            $prices = $this->service->calculatePrices(
                $singlePrice,
                $discount,
                $amount
            );

            // Define initial values for ext_date and package_id;
            $package = null;

            // Check package_id if exists in array
            if (array_key_exists('package_id', $data) && $data['package_id']) {
                $package = Package::findOrFail($data['package_id']);
            }

            // Defined order attributes before creating
            $orderAttributes = [
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'],
                'purchase_id' => $purchase->id,
                'sold_quantity' => $amount,
                'single_sold_price' => $singlePrice,
                'discount_single_sold_price' => $prices['discount_price'],
                'total_sold_price' => $prices['total_price'],
                'original_sold_price' => $prices['original_price'],
                'discount_percent' => $discount,
                'date_of_sale' => now()->parse($data['date_of_sale']),
                'tracking_number' => $data['tracking_number'],
                'package_id' => $package ?? null,
                'package_extension_date' => $package ? $package->expected_delivery_date : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $order->fill($orderAttributes);
            $order->save();
            $purchase->save();

            //Save the updated order and create or update the payment
            $this->createOrUpdatePayment($order);

            return $order; 
        }
    }

    /**
     * Create or update payment and associated invoice for the given order.
     *
     * @param Order $order The order for which payment and invoice need to be created or updated.
     *
     * @return Payment The created or updated payment instance.
     */

     private function createOrUpdatePayment(Order $order): OrderPayment
     {
         $alias = $this->service->getAlias($order);
         
         $paymentData = [
            'alias' => $alias ?: 'default_alias',
             'quantity' => $order->sold_quantity,
             'price' => $order->total_sold_price,
             'date_of_payment' => $order->package_extension_date ?: $order->date_of_sale,
             'payment_status' => self::INIT_STATUS,
         ];

         $payment = $order->payment 
         ? $order->payment->update($paymentData) 
         : $order->payment()->create($paymentData);

         $payment->invoice()->updateOrCreate([], [
             'price' => $payment->price,
             'quantity' => $payment->quantity
         ]);
     
         return $payment;
     }
     
}
