<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\OrderPayment;
use stdClass;

class OrderPaymentApiController extends Controller
{
    public function getData(Request $request)
    {
        $customerObj = new stdClass();
        $dateToString = null;

        $customer = isset($request->customer) && is_numeric($request->customer) ? $request->customer : null;
        $date = isset($request->date) && $request->date ? $request->date : null;

        $dates = $this->formatDateRange($date);
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        $paymentQ = OrderPayment::query()
            ->with('order.purchase','invoice')
            ->whereHas('order', function ($query) {
                $query->where('is_paid', 1)->where('status', 1);
            });

        if ($customer) {
            $paymentQ->whereHas('order', function ($query) use ($customer) {
                $query->where('customer_id', $customer);
            });

            $customerObj = Customer::with(['state:id,name', 'country:id,name,short_name'])->find($customer);
        }

        if ($dates) {
            $paymentQ->where('date_of_payment', '>=', $dates[0])
                ->where('date_of_payment', '<=', $dates[1]);

            $dateToString = date('F j, Y', strtotime($dates[0])) . ' - ' . date('F j, Y', strtotime($dates[1]));
        }
 
        $result = $paymentQ->skip($offset)->take($limit)->get();

        foreach ($result as $key => $payment) {
            $payment->payment_method = array_key_exists($payment->payment_method, config('statuses.payment_methods_statuses')) ? config('statuses.payment_methods_statuses.' . $payment->payment_method) : $payment->payment_method;
            $payment->payment_status = array_key_exists($payment->payment_status, config('statuses.payment_statuses')) ? config('statuses.payment_statuses.' . $payment->payment_status) : $payment->payment_status;
        }

        $filteredRecords = $paymentQ->count();
        $totalRecords = $paymentQ->count();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $result,
            'customer' => $customerObj,
            'date' => $dateToString,
            'sum' => number_format($paymentQ->sum('price'), 2, '.', '')
        ]);
    }

    private function formatDateRange($date)
    {
        if (!empty($date)) {
            $dates = explode(" - ", $date);
            $date1 = $dates[0];
            $date2 = $dates[1];

            $date1_formatted = date('Y-m-d 23:59:59', strtotime($date1));
            $date2_formatted = date('Y-m-d 23:59:59', strtotime($date2));

            if (strtotime($date1) !== false && strtotime($date2) !== false) {
                return [
                    $date1_formatted,
                    $date2_formatted
                ];
            }
        }

        return null;
    }
}
