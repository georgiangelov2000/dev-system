<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\OrderPayment;
use App\Http\Requests\OrderPaymentRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
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
        DB::beginTransaction();

        try {
            $purchaseIds = $request->product_id;
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

                if ($foundPurchase->quantity === 0) {
                    $foundPurchase->status = 0;
                }

                $foundPurchase->save();

                $finalSinglePrice = FunctionsHelper::calculatedDiscountPrice($orderSinglePrice, $orderDiscount);
                $finalPrice = FunctionsHelper::calculatedFinalPrice($finalSinglePrice, $orderQuantity);
                $originalPrice = FunctionsHelper::calculatedFinalPrice($orderSinglePrice, $orderQuantity);

                $order = [
                    'customer_id' => $customer,
                    'purchase_id' => $purchaseId,
                    'sold_quantity' => $orderQuantity,
                    'single_sold_price' => $finalSinglePrice,
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

            return response()->json(['message' => 'Order has been created'],200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been created'],200);
        }
    }

    public function edit(Order $order)
    {
        $currentOrder = $order->load('customer:id,name', 'purchase');

        return view('orders.edit', compact('currentOrder'));
    }

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $customer_id = (int) $request->customer_id;
            $date_of_sale = date('Y-m-d', strtotime($request->date_of_sale));
            $tracking_number = (string) $request->tracking_number;
            $purchase_id = (int) $request->product_id;
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

            if ($purchase->quantity === 0) {
                $purchase->status = 0;
            } else {
                $purchase->status = 1;
            }

            $purchase->save();

            $finalSinglePrice = FunctionsHelper::calculatedDiscountPrice($single_sold_price, $discount_percent);
            $finalTotalPrice = FunctionsHelper::calculatedFinalPrice($finalSinglePrice, $sold_quantity);

            $order->update([
                'customer_id' => $customer_id,
                'purchase_id' => $purchase_id,
                'sold_quantity' => $sold_quantity,
                'single_sold_price' => $finalSinglePrice,
                'total_sold_price' => $finalTotalPrice,
                'original_sold_price' => FunctionsHelper::calculatedFinalPrice($single_sold_price, $sold_quantity),
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

    public function updateStatus(Order $order, Request $request)
    {
        try {

            $specificColumns = $request->only(['status','detach_package']);

            $detachPackage = isset($specificColumns['detach_package'])
            && $specificColumns['detach_package'] == true ? true : false;
    
            $status = isset($specificColumns['status']) && ($specificColumns['status'] == 3 || $specificColumns['status'] == 4) 
            ? $specificColumns['status'] 
            : false;
                        
            if($detachPackage) {
                $package = $order->packages()->first();
                if($package) {
                    $order->packages()->detach($package->id);
                    $order->package_extension_date = null;
                }
            }
    
            if($status) {
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
