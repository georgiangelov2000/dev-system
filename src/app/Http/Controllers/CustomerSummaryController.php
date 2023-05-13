<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerSummaryService;
use App\Helpers\LoadStaticData;

class CustomerSummaryController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('summaries.customer_summary',[
            'customers' => $this->staticDataHelper->callCustomers()
        ]);
    }
    public function summary(Request $request)
    {
        $customer = $request->customer;
        $date = $request->date;
        $order_filter = $request->order_filter;
        $service = new CustomerSummaryService($customer, $date, $order_filter);
        $summary = $service->getSummary();

        return response()->json($summary);
        
    }
}
