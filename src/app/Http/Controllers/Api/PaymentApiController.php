<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FunctionsHelper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Models\Supplier;

use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    protected $helper;

    public function __construct(FunctionsHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getData(Request $request)
    {
        $type = isset($request->type) && $request->type ? $request->type : null;
        $date = isset($request->date) && $request->date ? $request->date : null;
        $id = isset($request->user) && $request->user ? $request->user : null;
        $package = isset($request->package) && $request->package ? $request->package : null;

        list ($dateStart, $dateEnd) = $this->helper->dateRange($date);

        if ($type === 'order') {
            $query =  $this->orderPayments($id, $dateStart, $dateEnd, $package);
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

    private function orderPayments($id, $dateStart, $dateEnd, $package): array
    {

        $paymentQuery = OrderPayment::query()->with('order.purchase', 'invoice');

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

        if ($package) {
            $paymentQuery->whereHas('order', function ($query) use ($package) {
                $query->where('package_id', $package);
            });
        }

        $result = $paymentQuery->skip(0)->take(10)->get();

        return [ // Return an associative array
            'data' => $result,
            'user' => $customer,
            'date' => $this->helper->dateToString($dateStart, $dateEnd),
            'sum' => number_format($paymentQuery->sum('price'), 2, '.', ''),
            'recordsTotal' => $paymentQuery->count(),
            'recordsFiltered' => $paymentQuery->count(),
        ];
    }

    private function purchasePayments($id, $dateStart, $dateEnd): array
    {
        $paymentQuery = PurchasePayment::query()
            ->with(
                ['purchase:id,name,supplier_id,quantity,price,total_price,initial_quantity,notes,code,status,image_path', 'invoice']
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
            'date' => $this->helper->dateToString($dateStart, $dateEnd),
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

}
