<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackageRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Package;
use App\Models\Order;
use App\Helpers\LoadStaticData;
use App\Models\Customer;
use App\Http\Requests\OrderPaymentMassRequest;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    private $staticDataHelper;
    const IS_IT_DELIVERED = 0;
    private $methods = [];
    private $types = [];
    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->methods = config('statuses.delivery_methods');
        $this->types = config('statuses.package_types');
    }
    public function index()
    {
        return view('packages.index', [
            'customers' => $this->staticDataHelper->callCustomers()
        ]);
    }

    public function create()
    {
        return view('packages.create');
    }


    public function store(PackageRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->packageProcessing($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Package has not been created');
        }

        return redirect()->route('packages.index')->with('success', 'Package has been created');
    }

    public function edit(Package $package)
    {
        $package->load('orders');
        return view('packages.edit', compact('package'));
    }

    public function update(Package $package, PackageRequest $request)
    {

        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->packageProcessing($data, $package);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update package');
        }

        return redirect()->route('packages.index')->with('success', 'Package updated successfully');
    }

    public function updateSpecificColumns(Package $package, Request $request)
    {
        $specificColumns = $request->only([
            'delivery_method',
            'package_type',
            'delivery_date'
        ]);
        
        try {
            
            if (isset($specificColumns['delivery_date']) && $specificColumns['delivery_date'] > $package['expected_delivery_date']) {
                $package->delivery_date = date('Y-m-d', strtotime($specificColumns['delivery_date']));
                $package->is_it_delivered = 1;
            }
            if (array_key_exists('delivery_method', $this->methods)) {
                $package->delivery_method = $specificColumns['delivery_method'] = $this->methods[$specificColumns['delivery_method']];
            }
            if (array_key_exists('package_type', $this->types)) {
                $package->package_type = $specificColumns['package_type'] = $this->types[$specificColumns['package_type']];
            }
            
            $package->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Package has not been updated'], 500);
        }

        return response()->json(['message' => 'Package has been updated'], 200);
    }

    public function orders(Package $package)
    {
        $package->load('orders');
        return view('packages.orders', compact('package'));
    }

    public function formOperations(){
        
    }

    public function updateFormOperations(OrderPaymentMassRequest $request){
        $data = $request->validated();
        
        $orderIds = $data['order_id'];

        foreach ($orderIds as $key => $value) {
            $order = Order::find($value);

            if(!$order) {
                throw new \Exception("Order has not been found");
            }
            
            if($order->is_it_delivered  !== 1) {
                throw new \Exception("Payment has been delivered");
            }

            $paymentMethod = $data['payment_method'][$key];
            $delivered = $data['is_it_delivered'][$key];
            $dateOfPayment = $data['date_of_payment'][$key];
            $deliveryDate = $data['delivery_date'][$key];
            $invoiceNumber = $data['invoice_number'][$key];
            $invoiceDate = $data['invoice_date'][$key];

    
            $packageExpectDateOfPayment = $order->package->

            $payment = $order->payment ? $order->payment : new OrderPayment();
            
            
            
        }
    }

    public function show(): \Illuminate\View\View
    {
        $packages = Package::select('id', 'package_name')->get();
        return view('packages.form_operations', ['packages' => $packages]);
    }
    

    public function destroy(Package $package)
    {
        DB::beginTransaction();
        try {
            $package->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Package has not been deleted'], 500);
        }
        return response()->json(['message' => 'Package has been deleted'], 200);
    }

    // Private methods
    private function packageProcessing(array $data, $package = null)
    {
        if (!array_key_exists($data['package_type'], $this->types)) {
            throw new \Exception("Invalid package type"); // You can provide a custom message here
        }

        if (!array_key_exists($data['delivery_method'], $this->methods)) {
            throw new \Exception("Invalid delivery method"); // You can provide a custom message here
        }

        $package = $package ? $package : new Package;
        $isNewPackage = !$package->exists; // Check if it's a new package

        $package->package_name = $data['package_name'];
        $package->tracking_number = $data['tracking_number'];
        $package->package_type = $data['package_type'];
        $package->delivery_method = $data['delivery_method'];
        $package->expected_delivery_date = now()->parse($data['expected_delivery_date']);
        $package->package_notes = $data['package_notes'] ?? '';
        $package->customer_notes = $data['customer_notes'] ?? '';
        $package->is_it_delivered = $isNewPackage ? self::IS_IT_DELIVERED : $package->is_it_delivered;

        $package->save();

        if (!empty($data['order_id'])) {
            Order::whereIn('id', $data['order_id'])->update([
                'package_extension_date' => $package->expected_delivery_date,
                'package_id' => $package->id
            ]);
        }
    }
}
