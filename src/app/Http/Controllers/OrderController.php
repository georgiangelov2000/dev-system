<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Product;
use App\Helpers\LoadStaticData;
use Datetime;

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
            $productIds = $request->product_id;
            $trackingNumber = (string) $request->tracking_number;
            $customer = (int) $request->customer_id;
            $orderDateOfSale = date('Y-m-d', strtotime($request->date_of_sale));
            $orderStatus = (int) $request->status;

            $orders = [];

            foreach ($productIds as $key => $productId) {
                $productId = (int) $productId;
                $orderQuantity = (int) $request['sold_quantity'][$key];
                $orderSinglePrice = (float) $request['single_sold_price'][$key];
                $orderDiscount = (int) $request['discount_percent'][$key];

                $foundProduct = Product::find($productId);

                if ($foundProduct->initial_quantity < $orderQuantity) {
                    return back()->with('error', 'Product quantity is not enough' . $foundProduct->name);
                };

                $foundProduct->quantity -= $orderQuantity;

                if ($foundProduct->quantity === 0) {
                    $foundProduct->status = 'disabled';
                }

                $foundProduct->save();

                $finalSinglePrice = FunctionsHelper::calculatedDiscountPrice($orderSinglePrice, $orderDiscount);
                $finalPrice = FunctionsHelper::calculatedFinalPrice($finalSinglePrice, $orderQuantity);
                $originalPrice = FunctionsHelper::calculatedFinalPrice($orderSinglePrice, $orderQuantity);

                $order = [
                    'customer_id' => $customer,
                    'product_id' => $productId,
                    'invoice_number' => (string) $request['invoice_number'][$key],
                    'sold_quantity' => $orderQuantity,
                    'single_sold_price' => $finalSinglePrice,
                    'total_sold_price' => $finalPrice,
                    'original_sold_price' =>  $originalPrice,
                    'discount_percent' => $orderDiscount,
                    'date_of_sale' => $orderDateOfSale,
                    'status' => $orderStatus,
                    'tracking_number' => $trackingNumber,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $orders[] = $order;
            }

            Order::insert($orders);
            DB::commit();
            return response()->json(['message' => 'Order has been created']);
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Order has not been created');
        }
    }

    public function edit(Order $order)
    {
        $currentOrder = $order->load('customer:id,name', 'product');

        return view('orders.edit', compact('currentOrder'));
    }

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $customer_id = (int) $request->customer_id;
            $date_of_sale = date('Y-m-d', strtotime($request->date_of_sale));
            $status = $request->status;
            $tracking_number = (string) $request->tracking_number;
            $invoice_number = (string) $request->invoice_number;
            $product_id = (int) $request->product_id;
            $sold_quantity = (int) $request->sold_quantity;
            $single_sold_price = (float) $request->single_sold_price;
            $discount_percent = (int) $request->discount_percent;

            $product = Product::with('orders')->findOrFail($order->product_id);
            $totalSoldQuantity = $product->orders->sum('sold_quantity');
            $remainingQuantity = $totalSoldQuantity - $order->sold_quantity;

            $updatedQuantity = ($remainingQuantity + $sold_quantity);

            if ($updatedQuantity > $product->initial_quantity) {
                return back()->with('error', 'Product quantity is not enough');
            }

            $finalQuantity = ($product->initial_quantity - $updatedQuantity);
            $product->quantity = $finalQuantity;

            if ($product->quantity === 0) {
                $product->status = 'disabled';
            } else {
                $product->status = 'enabled';
            }

            $product->save();

            $finalSinglePrice = FunctionsHelper::calculatedDiscountPrice($single_sold_price, $discount_percent);
            $finalTotalPrice = FunctionsHelper::calculatedFinalPrice($finalSinglePrice, $sold_quantity);

            $order->update([
                'customer_id' => $customer_id,
                'product_id' => $product_id,
                'invoice_number' => $invoice_number,
                'sold_quantity' => $sold_quantity,
                'single_sold_price' => $finalSinglePrice,
                'total_sold_price' => $finalTotalPrice,
                'original_sold_price' => FunctionsHelper::calculatedFinalPrice($single_sold_price, $sold_quantity),
                'discount_percent' => $discount_percent,
                'date_of_sale' => $date_of_sale,
                'status' => $status,
                'tracking_number' => $tracking_number,
            ]);
            DB::commit();
            return redirect()->route('order.index')->with('success', 'Order has been updated');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Order has not been updated');
        }
    }

    public function updateStatus(Order $order, Request $request)
    {
        $status = $request->status;

        try {
            $order->status = $status;
            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
        return response()->json([
            'data' => $status,
            'message' => 'Order has been updated'
        ], 200);
    }
    public function delete(Order $order)
    {
        DB::beginTransaction();

        try {
            $product = $order->product;
            $orderQuantity = $order->sold_quantity;

            if (!$product) {
                throw new \Exception("Product not found");
            }

            $product->quantity += $orderQuantity;
            $product->save();

            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('error', 'Order has not been deleted');
        }
        return response()->json(['message' => 'Order has been deleted'], 200);
    }

    public function createPayment()
    {
        return view('payments.create_customer_payments');
    }

    public function storePayment(PaymentRequest $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->id;
            
            if (count($ids)) {
                foreach ($ids as $key => $value) {

                    $order = Order::where('id', $value)->update([
                        'status' => 1,
                        'is_paid' => 1
                    ]);

                    if ($order > 0) {
                        CustomerPayment::create([
                            'order_id' => $value,
                            'date_of_payment' => date('Y-m-d', strtotime($request->date_of_payment[$key])),
                            'price' => $request->price[$key],
                            'quantity' => $request->quantity[$key],
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Package payment has not been created"], 500);
        }

        return response()->json(['message' => "Package payment has been created"], 200);
    }
}
