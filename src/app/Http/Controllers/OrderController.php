<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use App\Http\Requests\OrderMassEditRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Package;
use App\Models\Purchase;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {

            $data = $request->validated();

            if (empty($data['purchase_id'])) {
                return response()->json(['message' => 'Please add rows'], 400);
            }    

            if (count($data['purchase_id'])) {
                foreach ($data['purchase_id'] as $key => $id) {
                    $purchaseId = $data['purchase_id'][$key];
                    $purchase = Purchase::find($purchaseId);

                    if (!$purchase) {
                        return response()->json(['message' => "Purchase with ID $purchaseId not found"], 404);
                    }

                    $order = new Order;
                    $order->customer_id = $data['customer_id'];
                    $order->user_id = $data['user_id'];
                    $order->purchase_id = $purchase->id;
                    $expected_date_of_payment = now()->parse($data['expected_date_of_payment']);

                    if (array_key_exists('package_id', $data) && $data['package_id']) {
                        try {
                            $package = Package::findOrFail($data['package_id']);
                            $order->package_id = $package->id;
                            $order->package_extension_date = $package->expected_delivery_date;
                        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                            DB::rollback();
                            return response()->json(['message' => "Package not found for ID {$data['package_id']}"], 404);
                        }
                    }
 
                    $order->expected_delivery_date = now()->parse($data['expected_delivery_date']);

                    $amount = $data['sold_quantity'][$key];
                    $singlePrice = $data['single_sold_price'][$key];
                    $discount = $data['discount_percent'][$key];
                    $trackingNumber = $data['tracking_number'][$key];

                    $totalSoldAmount = $purchase->orders->sum('sold_quantity');
                    if ($amount > $purchase->quantity) {                        
                        return response()->json([
                            'message' => "Order not saved for purchase ID $purchaseId: Sold quantity exceeds available quantity.",
                            'purchase_id' => $purchaseId,
                            'available_quantity' => $purchase->quantity,
                        ], 400);
                    }

                    $updatedAmount = abs($purchase->quantity - $amount);
                    $purchase->quantity = $updatedAmount;

                    $order->tracking_number = $trackingNumber;
                    $order->sold_quantity = $amount;
                    $order->single_sold_price = $singlePrice;
                    $order = $this->service->calculatePrices($order);

                    $order->save();
                    $purchase->save();
                    $this->createOrUpdatePayment($order,$expected_date_of_payment);
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
        $paymentRecord = OrderPayment::where('order_id', $order->id)->first();
        $isEditable = $paymentRecord && $this->helper->statusValidation($paymentRecord->payment_status, $this->statuses) === Purchase::PENDING;
    
        if (!$isEditable) {
            $order->expected_delivery_date = now()->parse($order->expected_delivery_date)->format('d F Y');
            $order->payment->expected_date_of_payment = now()->parse($order->payment->expected_date_of_payment)->format('d F Y');
            $order->payment->delivery_date = now()->parse($order->payment->delivery_date)->format('d F Y');
        }
    
        $purchase = $order->purchase;
        $order->is_editable = $isEditable;
        
        if (!$isEditable) {
            $order->status = $this->statuses[$paymentRecord->payment_status];
        } else {
            $order->status = null;
        }
    
        $order->sum_of_orders_amount = $purchase->orders->sum('sold_quantity');
        $order->original_amount = $order->getOriginal('sold_quantity');
        
        return view('orders.edit', compact('order'));
    }
    

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            
            $order->customer_id = $data['customer_id'];
            $order->user_id = $data['user_id'];

            if($order->payment->payment_status === $order::PENDING) {
                $order->expected_delivery_date = now()->parse($data['expected_delivery_date']);
                    
                $newAmount = $data['sold_quantity'];
                $singlePrice = $data['single_sold_price'];
                $discount = $data['discount_percent'];
                $trackingNumber = $data['tracking_number'];
                $expectedDateOfPayment = now()->parse($data['expected_date_of_payment']);

                if (array_key_exists('package_id', $data) && $data['package_id']) {
                    try {
                        $package = Package::findOrFail($data['package_id']);
                        $order->package_id = $package->id;
                        $order->package_extension_date = $package->expected_delivery_date;
                    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                        return response()->json(['message' => "Package not found for ID {$data['package_id']}"], 404);
                    }
                }

                $order->tracking_number = $trackingNumber;
                $order->sold_quantity = $newAmount;

                // order new amount
                $amount = $order->sold_quantity;
                // Take related purchase model
                $purchase = $order->purchase;
                // Purchase initial amount
                $purchaseInitAmount = $purchase->initial_quantity;
                // Sum of orders amount
                $sumOfOrdersAmount = $purchase->orders->sum('sold_quantity');
                // Order amount before update
                $remainingOrderAmount = $order->getOriginal('sold_quantity');
                // Current purchase amount before sold quantity update
                $remainingAmount = ($sumOfOrdersAmount - $remainingOrderAmount);
                // dd($remainingAmount);
                //take updated amount based on remaining amount + updated amount
                $updatedAmount = ($remainingAmount + $amount);
                // dd($updatedAmount);
                // Check if the updated amount exceeds the initial purchase amount
                if ($updatedAmount > $purchaseInitAmount) {
                    return response()->json([
                        'message' => "Order not saved for purchase ID $purchase->id: Sold quantity exceeds available initial amount.",
                        'purchase_id' => $purchase->id,
                    ], 400);
                }

                // Update purchase amount and prices
                $finalAmount = ($purchaseInitAmount - $updatedAmount);
                // dd($purchaseInitAmount,$updatedAmount);
                $purchase->quantity = $finalAmount;
                $order->discount_percent = $discount; 
                
                // calculate final prices
                $order = $this->service->calculatePrices($order);


                // Save purchase related record
                $purchase->save();

                //Save the updated order and create or update the payment
                $this->createOrUpdatePayment($order,$expectedDateOfPayment);
            }
            
            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been updated'], 500);
        }
        return response()->json(['message' => 'Orders has been updated'], 200);
    }

    public function massUpdate(OrderMassEditRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $ids = $data['ids'] ;

            if(count($ids)) {
                $fieldsToUpdate = ['single_sold_price', 'sold_quantity', 'discount_percent', 'expected_delivery_date', 'expected_date_of_payment', 'package_id'];

                foreach ($ids as $key => $value) {
                    $order = Order::find($value);
                    $expectedDateOfPayment = null;
                    
                    foreach ($fieldsToUpdate as $field) {
                        if (isset($data[$field])) {

                            if ($field === 'expected_delivery_date') {
                                $order->$field = now()->parse($data[$field]);
                            }
                            elseif($field === 'expected_date_of_payment') {
                                $expectedDateOfPayment = now()->parse($data[$field]);
                            }
                            elseif($field === 'package_id') {
                                try {
                                    $package = Package::findOrFail($data['package_id']);
                                    $order->field = $package->id;
                                    $order->package_extension_date = $package->expected_delivery_date;
                                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                                    throw new NotFoundHttpException("Package not found for ID {$data['package_id']}");
                                }
                            } else {
                                $order->$field = $data[$field];
                            }
                            
                        }
                    }
                    
                    if(!$this->validateAndUpdatePurchase($order)) {
                        return response()->json([
                            'message' => "Order not saved for purchase ID {$order->purchase->id}: Sold quantity exceeds available quantity.",
                            'purchase_id' => $order->purchase->id,
                            'available_quantity' => $order->purchase->quantity,
                        ], 400);
                    }

                    $order = $this->service->calculatePrices($order);

                    $order->save();
                    $order->purchase->save();
                    
                    $this->createOrUpdatePayment($order,$expectedDateOfPayment);
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
     * Create or update payment and associated invoice for the given order.
     *
     * @param Order $order The order for which payment and invoice need to be created or updated.
     *
     * @return Payment The created or updated payment instance.
     */

    private function createOrUpdatePayment(Order $order, $expected_date_of_payment = null): OrderPayment
    {
        $payment = $order->payment ? $order->payment : new OrderPayment();

        // Generate an alias for the payment based on the order's delivery date
        $alias = $this->service->getAlias($order);

        $payment->alias = $alias;
        $payment->quantity = $order->sold_quantity;
        $payment->price = $order->total_sold_price;

        if($order->package_extension_date) {
            $expected_date_of_payment = $order->package_extension_date;
        }
        elseif(!isset($expected_date_of_payment)) {
            $expected_date_of_payment = $order->payment->expected_date_of_payment;
        }
        
        $payment->expected_date_of_payment = $expected_date_of_payment;

        if(!$payment->exists) {
            $payment->payment_status = self::INIT_STATUS;
        }
        
        $payment->save();

        $payment->invoice()->updateOrCreate([], [
            'price' => $payment->price,
            'quantity' => $payment->quantity,
        ]);

        return $payment;
    }

    private function validateAndUpdatePurchase(Order $order)
    {
        $purchase = $order->purchase;
        $amount = $order->sold_quantity;
        $purchaseInitAmount = $purchase->initial_quantity;
        $sumOfOrdersAmount = $purchase->orders->sum('sold_quantity');
        $remainingOrderAmount = $order->getOriginal('sold_quantity');
        $remainingAmount = ($sumOfOrdersAmount - $remainingOrderAmount);
        $updatedAmount = ($remainingAmount + $amount);

        if ($updatedAmount > $purchaseInitAmount) {
            return false;
        }

        // Update purchase amount and prices
        $finalAmount = ($purchaseInitAmount - $updatedAmount);
        $purchase->quantity = $finalAmount;

        return $order;
    }

}
