<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\LoadStaticData;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries()["countries"];
        $states = $this->staticDataHelper->callStatesAndCountries()["states"];

        return view('customers.index', [
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    public function create()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries()["countries"];
        $categories = $this->staticDataHelper->loadCallCategories();

        return view(
            'customers.create',
            ["countries" => $countries, "categories" => $categories]
        );
    }

    public function store(CustomerRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $customer = Customer::create($data);

            if ($customer) {
                $customerService = new CustomerService($customer);

                if ($request->file('image')) {
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

    public function edit(Customer $customer)
    {
        $service = new CustomerService($customer);

        $callStatesAndCountries = $this->staticDataHelper->callStatesAndCountries();

        $states = $callStatesAndCountries["states"];

        $countries = $callStatesAndCountries["countries"];

        return view('customers.edit', compact('customer'), [
            'countries' => $countries,
            'states' => $states,
            'relatedRecords' => $service->getEditData()
        ]);
    }

    public function update(Customer $customer, CustomerRequest $request)
    {

        $customerService = new CustomerService($customer);

        DB::beginTransaction();

        try {
            
            if ($request->hasFile('image')) {
                $customerService->imageUploader($request->file('image'));
            }
                        
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'zip' => $request->zip,
                'website' => $request->website,
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
                'notes' => $request->notes,
            ]);            

            DB::commit();

            Log::info('Succesfully updated cusotmer');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
        }


        return redirect()->route('customer.index')->with('success', 'Customer has been updated');
    }

    public function customerOrders(Customer $customer){
        $customerService = new CustomerService($customer);
        $orders = $customerService->getOrders();

        return view('customers.orders',compact('orders'));
    }

    public function updateOrders(Request $request)
    {
        $data = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'required|numeric',
            'single_sold_price' => 'required|array',
            'single_sold_price.*' => 'required|numeric',
            'sold_quantity' => 'required|array',
            'sold_quantity.*' => 'required|numeric',
            'discount' => 'required|array',
            'discount.*' => 'required|numeric',
        ], [
            'single_sold_price.*.required' => 'The single sold price is required.',
            'single_sold_price.*.numeric' => 'The single sold price must be a numeric value.',
            'sold_quantity.*.required' => 'The sold quantity is required.',
            'sold_quantity.*.numeric' => 'The sold quantity must be a numeric value.',
        ]);
        
        $orderIds = $data['order_ids'];
        $singleSoldPrices = $data['single_sold_price'];
        $soldQuantities = $data['sold_quantity'];
        $discounts = $data['discount'];

        DB::beginTransaction();
    
        try {
            foreach ($orderIds as $key => $orderId) {
                
                $newTotalPrice  = ($singleSoldPrices[$key] * $soldQuantities[$key]);

                $order = [
                    'id' => $orderId,
                    'single_sold_price' => $singleSoldPrices[$key],
                    'total_sold_price' => $newTotalPrice - ($newTotalPrice * ($discounts[$key] / 100)),
                    'sold_quantity' => $soldQuantities[$key],
                ];
                // Update the order in the database using the update query or Eloquent model
    
                // Example using Eloquent model:
                Order::where('id', $order['id'])->update([
                    'single_sold_price' => $order['single_sold_price'],
                    'total_sold_price' => $order['total_sold_price'],
                    'sold_quantity' => $order['sold_quantity'],
                ]);
            }
    
            DB::commit();

            return redirect()->route('customer.index')->with('success', 'Orders has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Orders has not been updated');
        }
    }

    public function delete(Customer $customer)
{
    DB::beginTransaction();
    try {

        if ($customer->orders()->exists()) {
            return response()->json(['message' => 'Customer has related orders and cannot be deleted'], 500);
        }

        $customer->orders()->delete();
        $customer_image = $customer->image;
        if ($customer_image) {
            $imagePath = storage_path('app/public/images/customers/' . $customer_image->name);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        };

        $customer->delete();
        DB::commit();
        Log::info('Successfully deleted customer');
        return response()->json(['message' => 'Customer has been deleted'], 200);   
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error deleting customer: ' . $e->getMessage());
        return response()->json(['message' => 'Customer has not been deleted', 'error' => $e->getMessage()], 500);
    }
}

    
}
