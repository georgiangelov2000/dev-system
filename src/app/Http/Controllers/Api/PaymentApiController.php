<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function getData(Request $request)
    {

        $type = isset($request->type) && $request->type ? $request->type : null;
        $id = isset($request->user) && $request->user ? $request->user : null;
        $date = isset($request->date) && $request->date ? $request->date : null;

        list($dateStart, $dateEnd) = $this->formatDateRange($date);

        if ($type === 'order') {
            $query =  $this->orderPayments($id, $dateStart, $dateEnd);
        } elseif ($type === 'purchase') {
            $query = $this->purchasePayments($id, $dateEnd, $dateEnd);
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $query['recordsTotal'],
            'recordsFiltered' => $query['recordsFiltered'],
            'data' => $query['data'],
            'user' => $query['user'],
            'date' => $query['date'],
            'sum' => $query['sum']
        ]);
    }

    private function orderPayments($id, $dateStart, $dateEnd): array
    {
        $paymentQuery = OrderPayment::query()->with('order.purchase','invoice');

        if ($id) {
            $paymentQuery->whereHas('order', function ($query) use ($id) {
                $query->where('customer_id', $id);
            });
            $customer = $this->customer($id);
        }

        if ($dateStart && $dateEnd) {
            $paymentQuery
                ->where('date_of_payment', '>=', $dateStart)
                ->where('date_of_payment', '<=', $dateEnd);
        }

        $result = $paymentQuery->skip(0)->take(10)->get();

        return [ // Return an associative array
            'data' => $result,
            'user' => $customer,
            'date' => $this->formatDateRangeForResponse($dateStart, $dateEnd),
            'sum' => number_format($paymentQuery->sum('price'), 2, '.', ''),
            'recordsTotal' => $paymentQuery->count(),
            'recordsFiltered' => $paymentQuery->count(),
        ];
    }

    private function purchasePayments($id, $dateStart, $dateEnd): array
    {
        $paymentQuery = PurchasePayment::query()
        ->with(
            ['purchase:id,name,supplier_id,quantity,price,total_price,initial_quantity,notes,code,status', 'invoice']
        );
        
        if ($id) {
            $paymentQuery->whereHas('purchase', function ($query) use ($id) {
                $query->where('supplier_id', $id);
            });
            $supplier = $this->supplier($id);
        }

        if ($dateStart && $dateEnd) {
            $paymentQuery
                ->where('date_of_payment', '>=', $dateStart)
                ->where('date_of_payment', '<=', $dateEnd);
        }

        $result = $paymentQuery->skip(0)->take(10)->get();

        return [ // Return an associative array
            'data' => $result,
            'user' => $supplier,
            'date' => $this->formatDateRangeForResponse($dateStart, $dateEnd),
            'sum' => number_format($paymentQuery->sum('price'), 2, '.', ''),
            'recordsTotal' => $paymentQuery->count(),
            'recordsFiltered' => $paymentQuery->count(),
        ];
    }

    private function customer($id)
    {
        return Customer::with(['state:id,name', 'country:id,name,short_name'])->find($id);
    }

    private function supplier($id)
    {
        return Supplier::with(['state:id,name', 'country:id,name,short_name'])->find($id);
    }

    private function formatDateRange($date): ?array
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

    private function formatDateRangeForResponse($dateStart, $dateEnd): ?string
    {
        // Format the date range for the response
        if ($dateStart && $dateEnd) {
            return date('F j, Y', strtotime($dateStart)) . ' - ' . date('F j, Y', strtotime($dateEnd));
        }

        return null;
    }
}
