<?php

namespace App\Http\Controllers;
use App\Helpers\LoadStaticData;
use App\Services\SupplierSummaryService;
use Illuminate\Http\Request;

class SupplierSummaryController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('summaries.supplier_summary',[
            'suppliers' => $this->staticDataHelper->callSupliers()
        ]);
    }

    public function summary(Request $request)
    {
        $supplier = $request->supplier;
        $date = $request->date;
        $service = new SupplierSummaryService($supplier, $date);
        $summary = $service->getSummary();

        return response()->json($summary);
        
    }
}
