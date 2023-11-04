<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\InvoicePurchase;
use App\Models\InvoiceOrder;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function update(InvoiceRequest $request, $type, $invoice)
    {
        DB::beginTransaction();

        try {
            $builder = $this->invoiceService->getInstance($invoice, $type);
            $data = $request->validated();
            $this->invoiceProcessing($data, $builder);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Invoice has not been updated'], 500);
        }
        return response()->json(['message' => 'Invoice has been updated'], 200);
    }

    private function invoiceProcessing(array $data, $builder)
    {
        $builder->fill([
            'invoice_number' => $data['invoice_number'],
            'invoice_date' => now()->parse($data['invoice_date']),
            'price' => $data['price'],
            'quantity' => $data['quantity']
        ])->save();

        return $builder;
    }
}
