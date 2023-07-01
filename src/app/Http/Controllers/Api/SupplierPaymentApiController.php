<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use stdClass;

class SupplierPaymentApiController extends Controller
{
    public function getData(Request $request)
    {
        $supplierObj = new stdClass();
        $dateToString = null;

        $supplier = isset($request->supplier) && is_numeric($request->supplier) ? $request->supplier : null;
        $date = isset($request->date) && $request->date ? $request->date : null;
        
        $dates = $this->formatDateRange($date);
        $offset = $request->input('start', 0);  
        $limit = $request->input('length', 10);
        
        $paymentQ = SupplierPayment::query()
            ->with(['purchase' => function ($query) {
                $query->select(
                    'id',
                    'name',
                    'supplier_id',
                    'quantity',
                    'price',
                    'total_price',
                    'initial_quantity',
                    'notes',
                    'code',
                    'status',
                    'is_paid'
                );
            }])
            ->whereHas('purchase', function ($query) {
                $query->where('is_paid', 1);
            });

        if ($supplier) {
            $paymentQ->whereHas('purchase', function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier);
            });

            $supplierObj = Supplier::select(
                'id',
                'name',
                'email',
                'phone',
                'address',
                'zip',
                'website',
                'state_id',
                'country_id'
            )->with(['state:id,name', 'country:id,name,short_name'])
                ->find($supplier);
        }

        if ($dates) {
            $paymentQ->where('date_of_payment', '>=', $dates[0])
                ->where('date_of_payment', '<=', $dates[1]);

            $dateToString = date('F j, Y', strtotime($dates[0])) . ' - ' . date('F j, Y', strtotime($dates[1]));
        }

        $result = $paymentQ->skip($offset)->take($limit)->get();

        if ($result->count()) {
            foreach ($result as $key => $payment) {
                $payment->payment_method = array_key_exists($payment->payment_method, config('statuses.payment_methods_statuses')) ? config('statuses.payment_methods_statuses.' . $payment->payment_method) : $payment->payment_method;
                $payment->payment_status = array_key_exists($payment->payment_status, config('statuses.payment_statuses')) ? config('statuses.payment_statuses.' . $payment->payment_status) : $payment->payment_status;
            }
        }

        $filteredRecords = $paymentQ->count();
        $totalRecords = $paymentQ->count();
        
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $result,
            'supplier' => $supplierObj,
            'date' => $dateToString,
            'sum' => number_format($paymentQ->sum('price'),2,'.','')
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
