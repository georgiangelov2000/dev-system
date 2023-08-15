<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Package;
use App\Models\Purchase;
use App\Helpers\LoadStaticData;

class OrderController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('orders.index');
    }
    public function create()
    {
        return view('orders.create');
    }

    public function store(OrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $purchaseIds = $request->purchase_id;
            $user = (int) $request->user_id;
            $trackingNumber = (string) $request->tracking_number;
            $customer = (int) $request->customer_id;
            $orderDateOfSale = date('Y-m-d', strtotime($request->date_of_sale));

            $orders = [];
            foreach ($purchaseIds as $key => $purchaseId) {
                $purchaseId = (int) $purchaseId;
                $orderQuantity = (int) $request['sold_quantity'][$key];
                $orderSinglePrice = (float) $request['single_sold_price'][$key];
                $orderDiscount = (int) $request['discount_percent'][$key];

                $foundPurchase = Purchase::find($purchaseId);

                if ($foundPurchase->initial_quantity < $orderQuantity) {
                    return back()->with('error', 'Purchase quantity is not enough' . $foundPurchase->name);
                };

                $foundPurchase->quantity -= $orderQuantity;

                $foundPurchase->save();

                $discountPrice = FunctionsHelper::calculatedDiscountPrice($orderSinglePrice, $orderDiscount);
                $finalPrice = FunctionsHelper::calculatedFinalPrice($discountPrice, $orderQuantity);
                $originalPrice = FunctionsHelper::calculatedFinalPrice($orderSinglePrice, $orderQuantity);

                $order = [
                    'customer_id' => $customer,
                    'user_id' => $user,
                    'purchase_id' => $purchaseId,
                    'sold_quantity' => $orderQuantity,
                    'single_sold_price' => $orderSinglePrice,
                    'discount_single_sold_price' => $discountPrice,
                    'total_sold_price' => $finalPrice,
                    'original_sold_price' =>  $originalPrice,
                    'discount_percent' => $orderDiscount,
                    'date_of_sale' => $orderDateOfSale,
                    'tracking_number' => $trackingNumber,
                    'status' => 6,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $orders[] = $order;
            }

            Order::insert($orders);
            DB::commit();

            return response()->json(['message' => 'Order has been created'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been created'], 500);
        }
    }

    public function edit(Order $order)
    {
        $order->load('customer:id,name', 'user:id,username', 'purchase.categories', 'purchase.brands','purchase.images');
        return view('orders.edit', compact('order'));
    }

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $customer_id = (int) $request->customer_id;
            $user = (int) $request->user_id;
            $date_of_sale = date('Y-m-d', strtotime($request->date_of_sale));
            $tracking_number = (string) $request->tracking_number;
            $purchase_id = (int) $request->purchase_id;
            $sold_quantity = (int) $request->sold_quantity;
            $single_sold_price = (float) $request->single_sold_price;
            $discount_percent = (int) $request->discount_percent;

            $purchase = Purchase::with('orders')->findOrFail($order->purchase_id);
            $totalSoldQuantity = $purchase->orders->sum('sold_quantity');
            $remainingQuantity = $totalSoldQuantity - $order->sold_quantity;

            $updatedQuantity = ($remainingQuantity + $sold_quantity);

            if ($updatedQuantity > $purchase->initial_quantity) {
                return back()->with('error', 'Purchase quantity is not enough');
            }

            $finalQuantity = ($purchase->initial_quantity - $updatedQuantity);

            $purchase->quantity = $finalQuantity;

            $purchase->save();

            $discountPrice = FunctionsHelper::calculatedDiscountPrice($single_sold_price, $discount_percent);
            $finalTotalPrice = FunctionsHelper::calculatedFinalPrice($discountPrice, $sold_quantity);
            $originalPrice = FunctionsHelper::calculatedFinalPrice($single_sold_price, $sold_quantity);

            $order->update([
                'customer_id' => $customer_id,
                'user_id' => $user,
                'purchase_id' => $purchase_id,
                'sold_quantity' => $sold_quantity,
                'single_sold_price' => $single_sold_price,
                'discount_single_sold_price' => $discountPrice,
                'total_sold_price' => $finalTotalPrice,
                'original_sold_price' => $originalPrice,
                'discount_percent' => $discount_percent,
                'date_of_sale' => $date_of_sale,
                'tracking_number' => $tracking_number,
            ]);
            DB::commit();
            return redirect()->route('order.index')->with('success', 'Order has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Order has not been updated');
        }
    }

    public function massUpdate(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|array',
            'order_id.*' => 'required|numeric',
            'price' => 'nullable|numeric',
            'sold_quantity' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'date_of_sale' => 'nullable|date',
            'package_id' => 'nullable|numeric'
        ]);

        DB::beginTransaction();

        try {
            $requestedQuantity = $data['sold_quantity'];
            $requestedPrice = $data['price'];
            $requestedDiscount = $data['discount_percent'];
            $requestedDateOfSale = isset($data['date_of_sale']) ? date('Y-m-d', strtotime($data['date_of_sale'])) : null;
            $requestedPackage = $data['package_id'];
            $orderIds = $data['order_id'];

            if (count($orderIds)) {

                foreach ($orderIds as $key => $order) {

                    // Check for exists order   
                    $order = Order::where('id', $order)
                        ->where('status', 6)
                        ->where('is_paid', 0)
                        ->firstOrFail();
                    
                    //Initial parameters 
                    $price = $order->single_sold_price;
                    $quantity = $order->sold_quantity;
                    $discount = $order->discount_percent;

                    // Find related purchase
                    $purchase = Purchase::with('orders')->findOrFail($order->first()->purchase_id);

                    // Rewrite initial parameters if the the requested date is exists;
                    if(isset($requestedPrice) && $requestedPrice) {
                        $price = $requestedPrice;
                    }
                    if(isset($requestedQuantity) && $requestedQuantity) {
                        $quantity = $requestedQuantity;
                    }
                    if(isset($requestedDiscount) && $requestedDiscount) {
                        $discount = $requestedDiscount;
                    }

                    //Calculate quantity of the purchase
                    $totalSoldQuantity = $purchase->orders->sum('sold_quantity');
                    $remainingQuantity = ($totalSoldQuantity - $order->first()->sold_quantity);
                    $updatedQuantity = ($remainingQuantity + $quantity);

                    if ($updatedQuantity > $purchase->initial_quantity) {
                        return response()->json(['message','Purchase quantity is not enough']);
                    }

                    $finalQuantity = ($purchase->initial_quantity - $updatedQuantity);
                    $purchase->quantity = $finalQuantity;
                    $purchase->save();

                    //Calculate price of the order
                    $finalSinglePrice = FunctionsHelper::calculatedDiscountPrice($price, $discount);
                    $finalPrice = FunctionsHelper::calculatedFinalPrice($finalSinglePrice, $quantity);
                    $originalPrice = FunctionsHelper::calculatedFinalPrice($price, $quantity);
                                        
                    // Save order parameters
                    $order->sold_quantity = $quantity;
                    $order->single_sold_price = $price;
                    $order->discount_single_sold_price = $finalSinglePrice;
                    $order->total_sold_price = $finalPrice;
                    $order->original_sold_price = $originalPrice;

                    if (isset($requestedDateOfSale) && $requestedDateOfSale) {
                        $order->date_of_sale = $requestedDateOfSale;
                    }

                    if (isset($requestedPackage) && $requestedPackage) {
                        $package = Package::findOrFail($requestedPackage);
                        
                        $package->orders()->attach([$order->id]);
                        $order->package_extension_date = date('Y-m-d', strtotime($package->expected_delivery_date));
                    }

                    $order->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'Orders has been updated'],200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Orders has not been updated',500]);
        }
    }

    public function updateStatus(Order $order, Request $request)
    {
        try {

            $specificColumns = $request->only(['status', 'detach_package']);

            $detachPackage = isset($specificColumns['detach_package'])
                && $specificColumns['detach_package'] == true ? true : false;

            $status = isset($specificColumns['status']) && ($specificColumns['status'] == 3 || $specificColumns['status'] == 4)
                ? $specificColumns['status']
                : false;

            if ($detachPackage) {
                $package = $order->packages()->first();
                if ($package) {
                    $order->packages()->detach($package->id);
                    $order->package_extension_date = null;
                }
            }

            if ($status) {
                $order->status = $status;
            }

            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been updated'], 500);
        }
        return response()->json(['message' => 'Order has been updated'], 200);
    }

    public function delete(Order $order)
    {
        DB::beginTransaction();

        try {
            $product = $order->product;
            $orderQuantity = $order->sold_quantity;

            if (!$product) {
                throw new \Exception("Purchase not found");
            }

            $product->quantity += $orderQuantity;
            $product->save();

            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Order has not been deleted');
        }
        return response()->json(['message' => 'Order has been deleted'], 200);
    }
}
