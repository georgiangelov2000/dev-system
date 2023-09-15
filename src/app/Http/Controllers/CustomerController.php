<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FunctionsHelper;
use App\Helpers\LoadStaticData;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
    private $staticDataHelper;
    private $helper;
    private $dir = 'public/images/customers';

    public function __construct(LoadStaticData $staticDataHelper, FunctionsHelper $helper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
    }

    public function index()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('states');
        $states = $this->staticDataHelper->callStatesAndCountries();

        return view('customers.index', compact('countries', 'states'));
    }

    public function create()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('customers.create', compact('countries', 'categories'));
    }

    public function store(CustomerRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->createOrUpdate($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Customer has not been created');
        }

        return redirect()->route('customer.index')->with('success', 'Customer has been created');
    }

    public function edit(Customer $customer)
    {
        $country = $customer->country_id;
        $states = $this->staticDataHelper->callStatesAndCountries($country, 'states');
        $countries = $this->staticDataHelper->callStatesAndCountries();

        return view('customers.edit', compact('customer', 'countries', 'states'));
    }

    public function update(Customer $customer, CustomerRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->createOrUpdate($data, $customer);
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
            $imageDeleted = $this->unlinkImage($customer->image_path);

            if ($imageDeleted) {
                $customer->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Customer has been deleted'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Customer has not been deleted', 'error' => $e->getMessage()], 500);
        }
    }

    public function customerOrders(Customer $customer)
    {
        $packages = Package::select('id', 'package_name', 'is_it_delivered')
            ->where('is_it_delivered', 0)
            ->get();

        $drivers = User::select('id','username')->where('role_id',2)->get();

        return view('customers.mass_edit_orders', compact('customer', 'packages', 'drivers'));
    }

    // Private methods;
    private function createOrUpdate(array $data, $customer = null)
    {
        $file = $data['image'] ?? null;
        
        $customer = $customer ?? new Customer;

        $customer->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'website' => $data['website'],
            'zip' => $data['zip'],
            'country_id' => $data['country_id'],
            'state_id' => $data['state_id'],
            'notes' => $data['notes'] ?? "",
        ]);
        
        if ($file) {
            $hash = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            if ($customer->image_path) {
                $this->unlinkImage($customer->image_path);
            }
            Storage::putFileAs($this->dir, $file, $hash);
            $customer->image_path = $this->helper->getImagePath($this->dir) . '/' . $hash;
        }

        $customer->save();
    }

    private function unlinkImage($path): bool
    {
        $imagePath = str_replace('/storage', '', $path);

        if (Storage::disk('public')->exists($imagePath) && Storage::disk('public')->delete($imagePath)) {
            return true;
        }

        return false;
    }

    
}
