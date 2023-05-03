<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerPaymentService;
use App\Helpers\LoadStaticData;

class CustomerPaymentController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index(){
        return view('payments.customer_payments',[
            'customers' => $this->staticDataHelper->callCustomers()
        ]);
    }

    public function summary(Request $request) {
        
    }

}
