<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SupplierRequest;
use App\Services\SupplierService;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Helpers\LoadStaticData;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller {

    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index() {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $states = $this->staticDataHelper->callStatesAndCountries('states');
        $categories = $this->staticDataHelper->loadCallCategories();
        
        return view('suppliers.index',[
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories
        ]);
    }

    public function create() {
        $countries = $this->staticDataHelper->callStatesAndCountries("countries");
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('suppliers.create', ["countries" => $countries,"categories"=>$categories]);
    }

    public function getState($countryId) {
        return response()->json(
            $this->staticDataHelper->callStatesAndCountries($countryId,'states')
        );
    }

    public function store(SupplierRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
 
        try {
            $supplier = Supplier::create($data);

            if ($supplier) {
                $supplierService = new SupplierService($supplier);

                if($request->file('image')){
                    $supplierService->imageUploader($request->file('image'));
                }
                            
                if ($supplier && isset($data['categories']) && count($data['categories'])) {
                    $supplierService->attachSupplierCategories( $data['categories'] );
                }
            }

            DB::commit();

            Log::info('Successfully created supplier');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier has been created');
    }

    public function edit(Supplier $supplier)
    {
        $service = new SupplierService($supplier);
        $country = $supplier->country_id;

        $categories = $this->staticDataHelper->loadCallCategories();
        $states = $this->staticDataHelper->callStatesAndCountries($country,'states');
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');

        $supplier = $supplier->load('image');

        return view('suppliers.edit', compact('supplier'), [
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories,
            'related_categories' => $supplier->categories->pluck('id')->toArray()
        ]);
    }

    public function update(Supplier $supplier, SupplierRequest $request) {

        $supplierService = new SupplierService($supplier);

        DB::beginTransaction();

        try {
            
            if ($request->hasFile('image')) {
                $supplierService->imageUploader($request->file('image'));
            }
                        
            if (isset($request->categories) && count($request->categories)) {
                $supplierService->attachSupplierCategories($request->categories);
            }
            
            $supplier->update([
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

            Log::info('Succesfully updated supplier');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
        }


        return redirect()->route('supplier.index')->with('success', 'Supplier has been updated');
    }

    public function delete(Supplier $supplier) {
        
        DB::beginTransaction();
        try {
            $supplierImage = $supplier->image;

            if ($supplierImage) {
                $imagePath = storage_path('app/public/images/suppliers/' . $supplierImage->name);
                
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

            }   
                     
            $supplier->delete();

            DB::commit();

            Log::info('Succesfully deleted supplier');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting supplier: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete supplier',$e->getMessage()], 500);
        }

        return response()->json(['message' => 'Supplier has been deleted'], 200);
    }
    
    public function detachCategory($id) {
        DB::beginTransaction();

        try {
            $related_category = SupplierCategory::findOrFail($id);
            
            $related_category->delete();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); 
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Failed to detach category'], 500);
        }
        return response()->json(['message' => 'Category has been detached'], 200);
    }

}
