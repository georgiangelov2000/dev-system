<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\LoadStaticData;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index(){
        $countries = $this->staticDataHelper->callStatesAndCountries()["countries"];
        $states = $this->staticDataHelper->callStatesAndCountries()["states"];
        
        return view('customers.index',[
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    public function create(){
        $countries = $this->staticDataHelper->callStatesAndCountries()["countries"];
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('customers.create',
            ["countries" => $countries,"categories"=>$categories]
        );
    }

    public function store(CustomerRequest $request){
        $data = $request->validated();
        
        DB::beginTransaction();

        try {
            $customer = Customer::create($data);

            if($customer) {
                $customerService = new CustomerService($customer);

                if($request->file('image')) {
                    $customerService->imageUploader($request->file('image'));
                }
            }

            DB::commit();

            Log::info('Successfully created customer');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage()); 
        }

        return redirect()->route('customer.index')->with('success', 'Customer has been created');
    }

    public function delete(Customer $customer) {
        
        DB::beginTransaction();
        try {
            $customerImage = $customer->image;

            if ($customerImage) {
                $imagePath = storage_path('app/public/images/customers/' . $customerImage->name);
                
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

            }   
                     
            $customer->delete();

            DB::commit();

            Log::info('Succesfully deleted customer');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting customer: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete customer',$e->getMessage()], 500);
        }

        return response()->json(['message' => 'Customer has been deleted'], 200);
    }

}
