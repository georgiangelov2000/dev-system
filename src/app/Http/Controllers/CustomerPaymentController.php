<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerPaymentService;
use App\Helpers\LoadStaticData;
use Illuminate\Support\Facades\View;

class CustomerPaymentController extends Controller
{
    private $staticDataHelper;

    private $date;
    private $customer;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index(){
        return view('payments.customer_payments',[
            'customers' => $this->staticDataHelper->callCustomers()
        ]);
    }

    public function payment(Request $request) {
        $this->date = $request->date;
        $this->customer = $request->customer;

        $paymentData = $this->getPayment();
        
        $customer_payments = [
            'data' => $paymentData
        ];

        $html = View::make('templates.payments',$customer_payments)->render();
        return response()->json(['html' => $html]);
    }

    public function getPayment(){
        $customerPaymentService = new CustomerPaymentService(
            $this->customer,
            $this->date
        );
        $result = $customerPaymentService->getData();
        return $result;
    }
}
