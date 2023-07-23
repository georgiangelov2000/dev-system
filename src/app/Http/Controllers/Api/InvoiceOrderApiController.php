<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceOrder;

class InvoiceOrderApiController extends Controller
{
    public function getData(Request $request)
    {
        $id = isset($request->id) ? $request->id : null;
        $invoiceQ = InvoiceOrder::query();

        if ($id) {
            $invoiceQ->where('id', $id);
        }

        $filteredRecords = $invoiceQ->count();
        $result = $invoiceQ->get();
        $totalRecords = InvoiceOrder::count();

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
