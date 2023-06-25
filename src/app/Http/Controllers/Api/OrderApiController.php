<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderApiController extends Controller
{
    public function getData(Request $request)
    {   $customer = isset($request->customer) && $request->customer ? $request->customer : null;
        $package = isset($request->package) && $request->package ? $request->package : null;
        $status = isset($request->status) && $request->status ? $request->status : null;
        $search = isset($request->search) && $request->search ? $request->search : null;
        $date_of_sale = isset($request->date_range) && $request->date_range ? $request->date_range :null;
        $date_of_payment = isset($request->date_of_payment) && $request->date_of_payment ? $request->date_of_payment :null;
        $select_json = isset($request->select_json) && $request->select_json ? $request->select_json : null;
        $order = isset($request->order_id) && $request->order_id ? $request->order_id : null;
        $product = isset($request->product_id) && $request->product_id ? $request->product_id : null;
        $withoutPackage = isset($request->withoutPackage) && $request->withoutPackage ? $request->withoutPackage : null;
        $isPaid = isset($request->is_paid) ? $request->is_paid : true;

        $offset = $request->input('start', 0);  
        $limit = $request->input('length', 10);

        $orderQuery = Order::query()->with(['customer:id,name','product:id,name','customerPayments']);
        
        $orderQuery->select(
            'id',
            'customer_id',
            'product_id',
            'invoice_number',
            'tracking_number',
            'sold_quantity',
            'single_sold_price',
            'total_sold_price',
            'original_sold_price',
            'discount_percent',
            'date_of_sale',
            'status',
            'is_paid',
            'package_extension_date',
            'created_at',
            'updated_at',
        );

        if ($customer) {
            $orderQuery->where('customer_id', $customer);
        }
        if($isPaid == false){
            $orderQuery->where('is_paid',0)->whereIn('status',[3,4]);
        }
        if($package) {
            $orderQuery->whereHas('package', function ($query) use ($package) {
                $query->where('package_id', $package);
            });
        }
        if($product) {
            $orderQuery->where('product_id',$product);
        }
        if($order) {
            $orderQuery->where('id',$order);
        }
        if($search) {
            $orderQuery->where('invoice_number', 'LIKE', '%'.$search.'%');
        }
        if($status) {
            $orderQuery->whereIn('status', $status);
        }
        if ($date_of_sale) {
            $date_pieces = explode(' - ',$date_of_sale);
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));
            
            //dd($date1_formatted);
            $orderQuery
            ->where('date_of_sale','>=',$date1_formatted)
            ->where('date_of_sale','<=',$date2_formatted);
        }
        if($date_of_payment) {
            $date_pieces = explode(' - ',$date_of_payment);
            $date1_formatted = date('Y-m-d 00:00:00', strtotime($date_pieces[0]));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date_pieces[1]));            

            $orderQuery->whereHas('customerPayments', function ($query) use ($date1_formatted,$date2_formatted) {
                $query
                ->where('date_of_payment', '>=', $date1_formatted)
                ->where('date_of_payment', '<=', $date2_formatted);
            });
        }
        if($withoutPackage) {
            $orderQuery->whereNull('package_id');
        }
        if($select_json) {
            return response()->json(
                $orderQuery->get()
            );
        }

        $filteredRecords = $orderQuery->count();
        $result = $orderQuery->skip($offset)->take($limit)->get();
        
        foreach ($result as $key => $order) {
            $order->status = array_key_exists($order->status, config('statuses.order_statuses')) ? config('statuses.order_statuses.' . $order->status) : $order->status;
            $order->is_paid = array_key_exists($order->is_paid, config('statuses.is_paid_statuses')) ? config('statuses.is_paid_statuses.' . $order->is_paid) : $order->is_paid;
            $order->package = $order->packages->first() ? $order->packages->first()->package_name : '';
        }

        $totalRecords = Order::count();

        return response()->json(
            [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $result
            ]
        );
    }
}
