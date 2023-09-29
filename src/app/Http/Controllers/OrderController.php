<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderMassEditRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Package;
use App\Models\Purchase;
use App\Services\OrderService;

class OrderController extends Controller
{
    private $service;

    const DELIVERED_STATUS = 6;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
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
                    $purchase = Purchase::find($id);

                    $orderQ = $data['sold_quantity'][$key];
                    $orderSinglePrice = $data['single_sold_price'][$key];
                    $orderDiscount = $data['discount_percent'][$key];

                    if ($purchase->initial_quantity < $orderQ) {
                        return back()->with('error', 'Purchase quantity is not enough' . $purchase->name);
                    }

                    $purchase->quantity -= $orderQ;

                    $purchase->save();

                    $prices = $this->service->calculatePrices(
                        $orderSinglePrice,
                        $orderDiscount,
                        $orderQ
                    );

                    $ext_date = null;
                    $package_id = null;

                    if (array_key_exists('package_id', $data) && $data['package_id']) {
                        $ext_date = Package::find($data['package_id'])->expected_delivery_date;
                        $package_id = $data['package_id'];
                    }

                    // Create an Order object
                    $order = Order::create([
                        'customer_id' => $data['customer_id'][$key],
                        'user_id' => $data['user_id'][$key],
                        'purchase_id' => $id,
                        'sold_quantity' => $orderQ,
                        'single_sold_price' => $orderSinglePrice,
                        'discount_single_sold_price' => $prices['discount_price'],
                        'total_sold_price' => $prices['total_price'],
                        'original_sold_price' =>  $prices['original_price'],
                        'discount_percent' => $data['discount_percent'][$key],
                        'date_of_sale' => now()->parse($data['date_of_sale']),
                        'tracking_number' => $data['tracking_number'],
                        'package_id' => $package_id,
                        'package_extension_date' => $ext_date,
                        'status' => self::DELIVERED_STATUS,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Call the createOrUpdatePayment method with the Order object
                    $this->service->createOrUpdatePayment($order);
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
            $this->orderUpdateProcessing($data, $order);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
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
                    $this->orderUpdateProcessing($data, $order);
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
    private function orderUpdateProcessing(array $data, $order)
    {
        // Update basic order information
        $this->updateBasicOrderInfo($data, $order);

        // Update completed order details if the order status is 'Delivered'
        if ($order->statusValidation() && $order->orderedStatus()) {
            $this->updateCompletedOrderInfo($data, $order);
        }

        // Save the updated order and create or update the payment
        $order->save();
        $this->service->createOrUpdatePayment($order);
    }


    /**
     * Update basic order information based on the provided data.
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    private function updateBasicOrderInfo(array $data, $order)
    {
        foreach ($order->defaultFields as $field) {
            $order->$field = $data[$field] ?? $order->$field;
        }

        // Update package extension date if a new package is selected
        if ($order->package_id) {
            $package = Package::find($order->package_id);
            $order->package_extension_date = $package->expected_delivery_date ?? $order->package_extension_date;
        }
    }

    /**
     * Update completed order details, including quantities, prices, and related calculations.
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    private function updateCompletedOrderInfo(array $data, $order)
    {
        foreach ($order->specificFields as $field) {
            if (isset($data[$field])) {
                // Convert 'date_of_sale' to Y-m-d format
                if ($field === 'date_of_sale' && $data[$field]) {
                    $order->$field = date('Y-m-d', strtotime($data[$field]));
                } else {
                    $order->$field = $data[$field];
                }
            }
        }

        $this->updateOrderQuantities($order);
    }

    /**
     * Update order quantities and related calculations.
     *
     * @param Order $order
     * @return void
     */
    private function updateOrderQuantities($order)
    {
        $newSingleSoldPrice = $order->single_sold_price;
        $newDiscountPercentage = $order->discount_percent;
        $newSoldQua = $order->sold_quantity;

        // Calculate total sold quantity and remaining quantity
        $totalSoldQuantity = $order->purchase->orders->sum('sold_quantity');
        $remainingQuantity = $totalSoldQuantity - $order->getOriginal('sold_quantity');
        $updatedQuantity = $remainingQuantity + $newSoldQua;

        // Check if the updated quantity exceeds the initial purchase quantity
        if ($updatedQuantity > $order->purchase->initial_quantity) {
            return response()->json(['message', 'Purchase quantity is not enough']);
        }

        // Update purchase quantity and prices
        $finalQuantity = $order->purchase->initial_quantity - $updatedQuantity;
        $order->purchase->quantity = $finalQuantity;
        $order->purchase->save();

        $prices = $this->service->calculatePrices($newSingleSoldPrice, $newDiscountPercentage, $newSoldQua);

        // Update order details with new quantities and prices
        $order->sold_quantity = $newSoldQua;
        $order->single_sold_price = $newSingleSoldPrice;
        $order->discount_single_sold_price = $prices['discount_price'];
        $order->total_sold_price = $prices['total_price'];
        $order->original_sold_price = $prices['original_price'];
    }
}
