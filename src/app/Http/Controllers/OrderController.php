<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class OrderController extends Controller
{

    public function index(){
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
            foreach ($data['product_id'] as $key => $productId) {

                $order = new Order();

                $order->customer_id = $data['customer_id'];
                $order->product_id = $productId;
                $order->invoice_number = $data['invoice_number'][$key];
                $order->sold_quantity = $data['sold_quantity'][$key];
                $order->single_sold_price = floatval($data['single_sold_price'][$key]);
                $order->total_sold_price = floatval($data['total_sold_price'][$key]);
                $order->discount_percent = $data['discount_percent'][$key];
                $order->date_of_sale = date('Y-m-d', strtotime($data['date_of_sale']));
                $order->status = $data['status'];

                $order->save();

                DB::commit();
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
        }

        return redirect()->back();
    }

    public function updateStatus(Order $order,Request $request) {
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
