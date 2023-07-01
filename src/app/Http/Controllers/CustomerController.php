<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\DB;
use App\Helpers\FunctionsHelper;
use App\Helpers\LoadStaticData;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Purchase;
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
        $countries = $this->staticDataHelper->callStatesAndCountries('states');
        $states = $this->staticDataHelper->callStatesAndCountries();

        return view('customers.index', [
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    public function create()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
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
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Customer has not been created');
        }

        return redirect()->route('customer.index')->with('success', 'Customer has been created');
    }

    public function edit(Customer $customer)
    {
        // $paymentOption = $request->option;

        // if($request->option === 'payment') {
        //     $customer->load('orders');
        //     dd($customer);
        // } else {
            $customer->load('image');
            $country = $customer->country_id;
            $states = $this->staticDataHelper->callStatesAndCountries($country,'states');
            $countries = $this->staticDataHelper->callStatesAndCountries();
    
            return view('customers.edit', compact('customer'), [
                'countries' => $countries,
                'states' => $states,
            ]);
        // }
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
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Customer has not been updated');
        }

        return redirect()->route('customer.index')->with('success', 'Customer has been updated');
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
            return response()->json(['message' => 'Customer has not been deleted', 'error' => $e->getMessage()], 500);
        }
    }

    public function customerOrders(Customer $customer)
    {
        $customerService = new CustomerService($customer);
        $result = $customerService->getOrders();
        return view('customers.orders', compact('result'));
    }

    public function updateCustomerOrders(Request $request)
    {
        $data = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'required|numeric',
            'single_sold_price' => 'required|array',
            'single_sold_price.*' => 'required|numeric',
            'sold_quantity' => 'required|array',
            'sold_quantity.*' => 'required|numeric',
            'discount_percent' => 'required|array',
            'discount_percent.*' => 'required|numeric',
        ], [
            'single_sold_price.*.required' => 'The single sold price is required.',
            'single_sold_price.*.numeric' => 'The single sold price must be a numeric value.',
            'sold_quantity.*.required' => 'The sold quantity is required.',
            'sold_quantity.*.numeric' => 'The sold quantity must be a numeric value.',
        ]);

        $orderIds = $data['order_ids'];
        DB::beginTransaction();

        try {
            foreach ($orderIds as $key => $orderId) {

                $singlePrice = (float) $data['single_sold_price'][$key];
                $soldQuantity = (int) $data['sold_quantity'][$key];
                $discount = (int) $data['discount_percent'][$key];

                $order = Order::where('id',$orderId);
                $purchase = Purchase::with('orders')->findOrFail($order->first()->purchase_id);

                $totalSoldQuantity = $purchase->orders->sum('sold_quantity');
                $remainingQuantity = ($totalSoldQuantity - $order->first()->sold_quantity);

                $updatedQuantity = ($remainingQuantity + $soldQuantity);
                
                if($updatedQuantity > $purchase->initial_quantity) {
                    return back()->with('error', 'Purchase quantity is not enough'.$purchase->name);
                }

                $finalQuantity = ($purchase->initial_quantity - $updatedQuantity);
                $purchase->quantity = $finalQuantity;
                
                $purchase->save();

                $finalSinglePrice = FunctionsHelper::calculatedDiscountPrice($singlePrice, $discount);
                $finalTotalPrice = FunctionsHelper::calculatedFinalPrice($finalSinglePrice, $soldQuantity);    
                
                Order::where('id',$order->first()->id)->update([
                    'single_sold_price' => $finalSinglePrice,
                    'total_sold_price' => $finalTotalPrice,
                    'original_sold_price' => FunctionsHelper::calculatedFinalPrice($singlePrice, $soldQuantity),
                    'sold_quantity' => $soldQuantity,
                    'discount_percent' => $discount,

                ]);
            }
            
            DB::commit();
            return redirect()->route('customer.index')->with('success', 'Orders has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Orders has not been updated');
        }
    }

}
