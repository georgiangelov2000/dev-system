<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackageRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Package;
use App\Models\Order;
use App\Helpers\LoadStaticData;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
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
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $orderIds = $data['order_id'];
            $delieveryDate = date('Y-m-d', strtotime($data['delievery_date']));

            $package = Package::create([
                'package_name' => $data['package_name'],
                'tracking_number' => $data['tracking_number'],
                'package_type' => $data['package_type'],
                'delievery_method' => $data['delievery_method'],
                'delievery_date' => $delieveryDate,
                'package_notes' => $data['package_notes'] ?? '',
                'customer_notes' => $data['customer_notes'] ?? '',
                'is_it_delivered' => 0,
            ]);

            $package->orders()->attach($orderIds);

            Order::whereIn('id', $orderIds)->update([
                'package_extension_date' => $delieveryDate,
                'package_id' => $package->id
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Package has not been created');
        }

        return redirect()->route('package.index')->with('success', 'Package has been created');
    }

    public function edit(Package $package)
    {
        $package->load('orders');
        return view('packages.edit', compact('package'));
    }

    public function update(PackageRequest $request, Package $package)
    {
        $data = $request->validated();
        
        DB::beginTransaction();

        try {
            $orderIds = $data['order_id'];
            $delieveryDate = date('Y-m-d', strtotime($data['delievery_date']));

            $package->update([
                'package_name' => $data['package_name'],
                'tracking_number' => $data['tracking_number'],
                'package_type' => $data['package_type'],
                'delievery_method' => $data['delievery_method'],
                'delievery_date' => $delieveryDate,
                'package_notes' => $data['package_notes'] ?? '',
                'customer_notes' => $data['customer_notes'] ?? '',
            ]);

            $package->orders()->sync($orderIds);

            Order::whereIn('id', $orderIds)->update([
                'date_of_sale' => $delieveryDate,
                'package_id' => $package->id
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Failed to update package');
        }

        return redirect()->route('package.index')->with('success', 'Package updated successfully');
    }

    public function status(Package $package, Request $request)
    {

        $delivery_method = $request->delivery_method;
        $package_type = $request->package_type;

        DB::beginTransaction();

        try {

            if (!is_null($delivery_method)) {
                $package->update([
                    'delievery_method' => $delivery_method
                ]);
            }
            if (!is_null($package_type)) {
                $package->update([
                    'package_type' => $package_type
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Package has not beed updated'], 500);
        }

        return response()->json(['message' => 'Package has been updated'], 200);
    }

    public function orders(Package $package) {
        $package->load('orders');
        return view('packages.orders',compact('package'));
    }

    public function delete(Package $package)
    {

        DB::beginTransaction();

        try {
            $package->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Package has not beed deleted'], 500);
        }
        return response()->json(['message' => 'Package has been deleted'], 200);
    }
}
