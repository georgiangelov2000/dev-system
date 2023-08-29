<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\DB;
use App\Helpers\LoadStaticData;
use App\Models\Customer;
use App\Models\Package;
use App\Models\CustomerImage;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    private $staticDataHelper;
    private $dir = 'public/images/customers';

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

        DB::beginTransaction();

        try {
            $data = $request->validated();
            $file = isset($data['image']) ? $data['image'] : false;
            $imagePath = Storage::url($this->dir);

            $customer = new Customer;
            $customer->name = $data['name'];
            $customer->email = $data['email'];
            $customer->phone = $data['phone'];
            $customer->address = $data['address'];
            $customer->website = $data['website'];
            $customer->zip = $data['zip'];
            $customer->country_id = $data['country_id'];
            $customer->state_id = $data['state_id'];
            $customer->notes = isset($data['notes']) ? $data['notes'] : "";
            $customer->website = isset($data['website']) ? $data['website'] : "";

            if ($file) {
                $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);
                $customer->image_path = $imagePath.'/'.$hashed_image;
            }

            $customer->save();
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

        return view('customers.edit', compact('customer'), [
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    public function update(Customer $customer, CustomerRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $file = isset($data['image']) ? $data['image'] : false;
            $imagePath = Storage::url($this->dir);

            $customer->name = $data['name'];
            $customer->email = $data['email'];
            $customer->phone = $data['phone'];
            $customer->address = $data['address'];
            $customer->website = $data['website'];
            $customer->zip = $data['zip'];
            $customer->country_id = $data['country_id'];
            $customer->state_id = $data['state_id'];
            $customer->notes = isset($data['notes']) ? $data['notes'] : "";
            $customer->website = isset($data['website']) ? $data['website'] : "";

            if ($file) {
                $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                $current_image = null;
                
                if ($customer->image_path) {
                    $current_image = $this->dir . DIRECTORY_SEPARATOR . $customer->image_path;
                    if (Storage::exists($current_image)) {
                        Storage::delete($current_image);
                    }
                    Storage::putFileAs($this->dir, $file, $hashed_image);
                    $customer->image_path = $imagePath .'/'. $hashed_image;
                } else {
                    Storage::putFileAs($this->dir, $file, $hashed_image);
                    $customer->image_path = $imagePath .'/'. $hashed_image;
                }
            }

            $customer->save();
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

            if ($customer->orders->count() > 0) {
                return response()->json(['message' => 'Customer has related orders and cannot be deleted'], 500);
            }

            if ($customer->image_path) {
                $imagePath = storage_path('app/public/images/customers/' . $customer->image_path);

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            };

            $customer->delete();
            DB::commit();
            return response()->json(['message' => 'Customer has been deleted'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Customer has not been deleted', 'error' => $e->getMessage()], 500);
        }
    }

    public function customerOrders(Customer $customer)
    {
        $packages = Package::select('id','package_name','is_it_delivered')
        ->where('is_it_delivered',0)
        ->get();
        return view('customers.mass_edit_orders',compact('customer','packages'));
    }
}
