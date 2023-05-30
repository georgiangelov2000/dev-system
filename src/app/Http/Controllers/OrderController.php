<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Product;
use App\Helpers\LoadStaticData;
use DateTime;

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
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $orders = [];

            foreach ($data['product_id'] as $key => $productId) {
                
                $product = Product::find($productId);

                if (!$product || $product->quantity <= 0) {
                    continue;
                }
                
                $singleSoldPrice = $data['single_sold_price'][$key];
                $quantity = $data['sold_quantity'][$key];
                $discount = $data['discount_percent'][$key];
                
                $product->quantity -= $quantity;
                $product->save();

                $finalSinglePrice = $this->calculatedDiscountPrice($singleSoldPrice,$discount);
                $finalTotalPrice = $this->calculatedFinalPrice($finalSinglePrice,$quantity);
                                
                $order = [
                    'customer_id' => $data['customer_id'],
                    'product_id' => $productId,
                    'invoice_number' => $data['invoice_number'][$key],
                    'sold_quantity' => $quantity,
                    'single_sold_price' => $finalSinglePrice,
                    'total_sold_price' => $finalTotalPrice,
                    'discount_percent' => $discount,
                    'date_of_sale' => date('Y-m-d', strtotime($data['date_of_sale'])),
                    'status' => $data['status'],
                    'tracking_number' => $data['tracking_number'],
                    'package_id' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $orders[] = $order;

            }

            DB::table('orders')->insert($orders);
            DB::commit();
            return redirect()->route('order.index')->with('success', 'Order has been created');
        } catch (\Exception $e) {
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
        $validated = $request->validated();
        DB::beginTransaction();
                
        try {
            $currentQuantity = $order->sold_quantity;
            $quantityDifference = $validated['sold_quantity'][0] - $currentQuantity;
            $product = $order->product;
            
            if ($currentQuantity > $validated['sold_quantity'][0] && $currentQuantity !== $validated['sold_quantity'][0]) {
                $product->quantity = ($product->initial_quantity - $validated['sold_quantity'][0] );
            } elseif ($currentQuantity < $validated['sold_quantity'][0] && $currentQuantity !== $validated['sold_quantity'][0]) {
                $product->quantity = ($product->quantity - $quantityDifference) ;
            }   
            $product->save();

            $finalSinglePrice = $this->calculatedDiscountPrice($validated['single_sold_price'][0],$validated['discount_percent'][0]);
            $finalTotalPrice = $this->calculatedFinalPrice($finalSinglePrice,$validated['sold_quantity'][0]);

            $order->update([
                'customer_id' => $validated['customer_id'],
                'product_id' => $validated['product_id'][0],
                'invoice_number' => $validated['invoice_number'][0],
                'sold_quantity' => $validated['sold_quantity'][0],
                'single_sold_price' => $finalSinglePrice,
                'total_sold_price' => $finalTotalPrice,
                'discount_percent' => $validated['discount_percent'][0],
                'date_of_sale' => date('Y-m-d', strtotime($validated['date_of_sale'])),
                'status' => $validated['status'],
                'tracking_number' => $validated['tracking_number'],
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
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
        }
        return response()->json([
            'data' => $status,
            'message' => 'Order has been updated'
        ], 200);
    }

    public function markAsPaid(Order $order, Request $request)
    {
        if ($order->is_paid) {
            return response()->json(['message' => 'This order has already been paid'], 200);
        }
    
        $validatedData = $request->validate([
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $date = new DateTime($validatedData['date']);
        $formatted_date = $date->format('Y-m-d');

        DB::beginTransaction();
    
        try {
            $order->is_paid = true;
            $order->save();
    
            $order->customerPayments()->create([
                'price' => $validatedData['price'],
                'date_of_payment' => $formatted_date
            ]);
    
            DB::commit();
    
            return response()->json(['message' => 'Payment for order has been successfully processed'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
    
            Log::error($e->getMessage());
    
            return response()->json(['message' => 'Payment for order has not been successfully processed'], 500);
        }
    }

    public function delete(Order $order)
    {
        $productId = $order->product_id;

        $quantity = $order->sold_quantity;
    
        DB::beginTransaction();
    
        try {
    
            $product = Product::find($productId);
    
            if (!$product) {
                throw new \Exception("Product not found");
            }
    
            $product->quantity += $quantity;
            $product->save();
    
            $order->delete();
    
            DB::commit();
    
            return redirect()->route('orders.index')->with('success', 'Order has been deleted');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('error', 'Order has not been deleted');
        }
    }

    private function calculatedDiscountPrice($price,$discount){
        $discountAmount = 0;
        $finalPrice = 0;

        if ( ($price && $discount) && (is_numeric($price) && is_numeric($discount)) ){
            $discountAmount = (($price * $discount) / 100);
            $finalPrice = ($price - $discountAmount);
        } else {
            $finalPrice = $price;
        }

        return $finalPrice;
    }

    private function calculatedFinalPrice($finalSingleSoldPrice, $quantity){
        $finalPrice = 0;

        if ( ($finalSingleSoldPrice && $quantity) && (is_numeric($finalSingleSoldPrice) && is_numeric($quantity)) ) 
            {
                $finalPrice = ($finalSingleSoldPrice * $quantity);
            }

        return $finalPrice;
    }


}
