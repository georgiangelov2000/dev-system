<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Helpers\LoadStaticData;
use App\Services\SupplierService;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{

    private $staticDataHelper;
    private $dir = 'public/images/suppliers';
    private $helper;
    private $supplierService;
    private $csvImporter;
    /**
     * Constructor to initialize class dependencies.
     *
     * @param LoadStaticData $staticDataHelper
     * @param FunctionsHelper $helper
     * @param SupplierService $supplierService
     */
    public function __construct(
        LoadStaticData $staticDataHelper,
        FunctionsHelper $helper,
        SupplierService $supplierService,
    ) {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
        $this->supplierService = $supplierService;
    }

    /**
     * Display the index view for suppliers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $states = $this->staticDataHelper->callStatesAndCountries('states');
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('suppliers.index', [
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories
        ]);
    }

    /**
     * Display the create view for a new supplier.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries("countries");
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('suppliers.create', ["countries" => $countries, "categories" => $categories]);
    }

    /**
     * Store a newly created supplier in the database.
     *
     * @param SupplierRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SupplierRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->supplierProcessing($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Supplier has not been created');
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier has been created');
    }

    /**
     * Display the edit view for a supplier.
     *
     * @param Supplier $supplier
     * @return \Illuminate\View\View
     */
    public function edit(Supplier $supplier)
    {
        $country = $supplier->country_id;

        $categories = $this->staticDataHelper->loadCallCategories();
        $states = $this->staticDataHelper->callStatesAndCountries($country, 'states');
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');

        return view('suppliers.edit', compact('supplier'), [
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories,
            'related_categories' => $supplier->categories->pluck('id')->toArray()
        ]);
    }

    /**
     * Update an existing supplier in the database.
     *
     * @param Supplier $supplier
     * @param SupplierRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Supplier $supplier, SupplierRequest $request)
    {
        // Update an existing supplier in the database and handle any errors.
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $this->supplierProcessing($data, $supplier);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Supplier has not been updated');
        }


        return redirect()->route('supplier.index')->with('success', 'Supplier has been updated');
    }

    /**
     * Delete a supplier from the database.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Supplier $supplier)
    {
        // Delete a supplier and handle any errors.

        DB::beginTransaction();
        try {
            if ($supplier->image_path) {
                $this->helper->deleteImage($supplier);
            }
            $supplier->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Supplier has not been deleted'], 500);
        }

        return response()->json(['message' => 'Supplier has been deleted'], 200);
    }

    /**
     * Detach a category from a supplier.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detachCategory($id)
    {
        // Detach a category from a supplier and handle any errors.
        DB::beginTransaction();

        try {
            $related_category = SupplierCategory::findOrFail($id);
            $related_category->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Category has not been detached'], 500);
        }
        return response()->json(['message' => 'Category has been detached'], 200);
    }

    public function massEdit(Supplier $supplier)
    {
        $categories = $supplier->categories()->get();
        $brands = $this->staticDataHelper->callBrands();
        return view('suppliers.mass_edit_purchases', ['supplier' => $supplier, 'categories' => $categories, 'brands' => $brands]);
    }
    
    /**
     * Process supplier data and store/update it in the database.
     *
     * @param array $data
     * @param Supplier|null $supplier
     * @return void
     */
    private function supplierProcessing(array $data, $supplier = null)
    {
        $supplier = $supplier ? $supplier : new Supplier;

        $supplier->name = $data['name'];
        $supplier->email = $data['email'];
        $supplier->phone = $data['phone'];
        $supplier->address = $data['address'];
        $supplier->website = $data['website'];
        $supplier->zip = $data['zip'];
        $supplier->country_id = $data['country_id'];
        $supplier->state_id = $data['state_id'];
        $supplier->notes = isset($data['notes']) ? $data['notes'] : "";
        $supplier->website = isset($data['website']) ? $data['website'] : "";

        if (isset($data['image'])) {
            $this->helper->imageUploader($data['image'], $supplier, $this->dir);
        }

        $supplier->save();

        $this->supplierService->syncCategories($data['categories'], $supplier);
    }
}
