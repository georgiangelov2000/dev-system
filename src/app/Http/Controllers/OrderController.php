<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
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

                $order = [
                    'customer_id' => $data['customer_id'],
                    'product_id' => $productId,
                    'invoice_number' => $data['invoice_number'][$key],
                    'sold_quantity' => $data['sold_quantity'][$key],
                    'single_sold_price' => $data['single_sold_price'][$key],
                    'total_sold_price' => $data['total_sold_price'][$key],
                    'discount_percent' => $data['discount_percent'][$key],
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

    public function edit(Order $order)
    {
        $currentOrder = $order->load('customer:id,name', 'product');

        return view('orders.edit', compact('currentOrder'));
    }

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();

        try {

            $validated = $request->validated();
            
            $order->customer_id = $validated['customer_id'];
            $order->date_of_sale = date('Y-m-d', strtotime($validated['date_of_sale']));
            $order->status = $validated['status'];

            foreach ($validated['product_id'] as $index => $value) {
                $order->product->id = $validated['product_id'][$index];
                $order->invoice_number = $validated['invoice_number'][$index];
                $order->sold_quantity = $validated['sold_quantity'][$index];
                $order->single_sold_price = $validated['single_sold_price'][$index];
                $order->total_sold_price = $validated['total_sold_price'][$index];
                $order->discount_percent = $validated['discount_percent'][$index];
                $order->tracking_number = $validated['tracking_number'];
            }

            $order->save();
            DB::commit();
            return redirect()->route('order.index')->with('success', 'Order has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Failed to update order');
        }
    }

    public function markAsPaid(Order $order, Request $request)
    {
        if ($order->is_paid) {
            return response()->json(['message' => 'This order has already been paid'], 200);
        }
    
        $validatedData = $request->validate([
            'price' => 'required|numeric|min:0',
            'customer' => 'required|exists:customers,id',
            'date' => 'required|date',
        ]);

        $date = new DateTime($validatedData['date']);
        $formatted_date = $date->format('Y-m-d');

        DB::beginTransaction();
    
        try {
            $order->is_paid = true;
            $order->save();
    
            $order->customerPayments()->create([
                'customer_id' => $validatedData['customer'],
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

        DB::beginTransaction();

        try {
            $order->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Failed to delete order'], 500);
        }
        return response()->json(['message' => 'Order has been deleted'], 200);
    }
}
