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
    private $dir = 'public/images/customers'; // Directory for customer images

    public function __construct(LoadStaticData $staticDataHelper, FunctionsHelper $helper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
    }

    /**
     * Display the customer index view with countries and states data.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('states');
        $states = $this->staticDataHelper->callStatesAndCountries();

        return view('customers.index', compact('countries', 'states'));
    }

    /**
     * Display the customer creation view with countries and categories data.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('customers.create', compact('countries', 'categories'));
    }

    /**
     * Store a new customer in the database.
     *
     * @param CustomerRequest $request The validated customer request data.
     * @return \Illuminate\Http\RedirectResponse A redirect response indicating the result of the store operation.
     */
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

    /**
     * Retrieve and display the customer edit view with data for editing.
     *
     * @param Customer $customer The customer to be edited.
     * @return \Illuminate\View\View
     */
    public function edit(Customer $customer)
    {
        $customer->load('country:id,name,short_name','state:id,name');
        return view('customers.edit',compact('customer'));
    }

    /**
     * Update an existing customer in the database.
     *
     * @param Customer $customer The customer to be updated.
     * @param CustomerRequest $request The validated customer request data.
     * @return \Illuminate\Http\RedirectResponse A redirect response indicating the result of the update operation.
     */
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

    /**
     * Delete a customer from the database, including its associated image if present.
     *
     * @param Customer $customer The customer to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the customer deletion operation.
     */
    public function delete(Customer $customer)
    {
        DB::beginTransaction();

        try {
            if ($customer->image_path) {
                $this->helper->deleteImage($customer);
            }
            $customer->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Customer has not been deleted', 'error' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Customer has been deleted'], 200);
    }

    /**
     * Display the customer orders view with available packages and drivers data.
     *
     * @param Customer $customer The customer for whom orders are being managed.
     * @return \Illuminate\View\View
     */
    public function customerOrders(Customer $customer)
    {
        $packages = Package::select('id', 'package_name', 'is_it_delivered')
            ->where('is_it_delivered', 0)
            ->get();

        $drivers = User::select('id', 'username')->where('role_id', 2)->get();

        return view('customers.mass_edit_orders', compact('customer', 'packages', 'drivers'));
    }

    // Private methods;

    /**
     * Create or update a customer record in the database.
     *
     * @param array $data The customer data to be processed.
     * @param Customer|null $customer The customer to be updated (optional).
     */
    private function createOrUpdate(array $data, $customer = null)
    {
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

        if (isset($data['image'])) {
            $this->helper->imageUploader($data['image'], $customer, $this->dir,'image_path');
        }

        $customer->save();
    }
}
