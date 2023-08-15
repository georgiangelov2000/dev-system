<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\DB;
use App\Helpers\LoadStaticData;
use App\Models\Customer;
use App\Models\Package;
use App\Models\CustomerImage;
use Illuminate\Support\Str;
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
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $file = isset($data['image']) ? $data['image'] : false;

            if (!isset($data['notes'])) {
                $data['notes'] = "";
            }
            if (!isset($data['website'])) {
                $data['website'] = "";
            }

            $customer = Customer::create($data);

            if ($file) {
                $hashed_image = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $imagePath = Storage::url($this->dir);
                Storage::putFileAs($this->dir, $file, $hashed_image);

                $image = new CustomerImage([
                    'path' => $imagePath,
                    'name' => $hashed_image
                ]);

                $customer->image()->save($image);
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
        $customer->load('image');
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
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $file = isset($data['image']) ? $data['image'] : false;
            $imagePath = Storage::url($this->dir);

            if (!isset($data['notes'])) {
                $data['notes'] = "";
            }
            if (!isset($data['website'])) {
                $data['website'] = "";
            }

            if ($file) {
                $hashed_image = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $current_image = null;
                
                if ($customer->image) {
                    $current_image = $this->dir . DIRECTORY_SEPARATOR . $customer->image->name;
                    
                    if (Storage::exists($current_image)) {
                        Storage::delete($current_image);
                    }

                    Storage::putFileAs($this->dir, $file, $hashed_image);

                    $customer->image->name = $hashed_image;
                    $customer->image->path = $imagePath;
                    $customer->image->save();
                } else {
                    Storage::putFileAs($this->dir, $file, $hashed_image);

                    $image = new CustomerImage([
                        'path' => $imagePath,
                        'name' => $hashed_image
                    ]);

                    $customer->image()->save($image);
                }
            }

            $customer->update($data);

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

            $customer_image = $customer->image;            
            if ($customer_image) {
                $imagePath = storage_path('app/public/images/customers/' . $customer_image->name);

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
