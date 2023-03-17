<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\State;
use App\Models\Country;
use App\Models\SupplierImage;
use App\Models\SupplierCategory;
use App\Helpers\LoadStaticData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller {

    protected $storage_static_files = 'public/images/suppliers';

    public function index() {
        $countries = LoadStaticData::loadCallStatesAndCountries()["countries"];
        $states = LoadStaticData::loadCallStatesAndCountries()["states"];
        $categories = LoadStaticData::loadCallCategories();
        
        return view('suppliers.index',[
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories
        ]);
    }

    public function create() {
        $countries = LoadStaticData::loadCallStatesAndCountries()["countries"];
        $categories = LoadStaticData::loadCallCategories();
        return view('suppliers.create', ["countries" => $countries,"categories"=>$categories]);
    }

    public function getState($countryId) {
        return response()->json(LoadStaticData::loadCallStatesAndCountries($countryId)["states"]);
    }

    public function store(SupplierRequest $request) {
        $data = $request->validated();

        $image = $request->file('image');
        $hashedImage = Str::random(10) . '.' . $image->getClientOriginalExtension();
        
        DB::beginTransaction();
 
        try {
            $supplier = Supplier::create($data);

            if ($supplier) {
                $supplierImage = SupplierImage::create([
                            'supplier_id' => $supplier->id,
                            'path' => config('app.url') . '/storage/images/suppliers/',
                            'name' => $hashedImage,
                ]); 

                if ($supplierImage) {
                    if (!Storage::exists($this->storage_static_files)) {
                        Storage::makeDirectory($this->storage_static_files);
                    }
                    Storage::putFileAs($this->storage_static_files, $image, $hashedImage);
                }
                                
                if ($supplier && isset($data['categories']) && count($data['categories'])) {
                    foreach ($data['categories'] as $subcategoryId) {
                        SupplierCategory::create([
                            'supplier_id' => $supplier->id,
                            'category_id' => $subcategoryId
                        ]);
                }
            }
            }

            DB::commit();

            Log::info('Successfully created supplier');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier has been created');
    }

    public function edit(Supplier $supplier)
    {

        $callStatesAndCountries = LoadStaticData::loadCallStatesAndCountries($supplier->country_id);
        $countries = $callStatesAndCountries["countries"];
        $states = [];
        
        if($supplier->country_id) {
            $states = $callStatesAndCountries["states"];
        }
        
        $supplier->load('image:id,supplier_id,path,name');
        
        $categories = LoadStaticData::loadCallCategories();

        return view('suppliers.edit', compact('supplier'), [
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories
        ]);
    }

    public function update(Supplier $supplier, SupplierRequest $request) {

        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $hashedImage = Str::random(10) . '.' . $image->getClientOriginalExtension();

                $supplierImage = $supplier->image;

                if ($supplierImage) {
                    $currentFile = Storage::exists($this->storage_static_files . '/' . $supplierImage->name);

                    if ($currentFile) {
                        Storage::delete($this->storage_static_files . '/' . $supplierImage->name);
                    }
                } else {
                    $supplierImage = new SupplierImage;
                    $supplierImage->supplier_id = $supplier->id;
                }   

                $supplierImage->path = config('app.url').'/storage/images/suppliers/' ;
                $supplierImage->name = $hashedImage;
                $supplierImage->save();

                Storage::putFileAs($this->storage_static_files, $image, $hashedImage);
            }
            
                        
            if (isset($request->categories) && count($request->categories)) {
                $supplier->categories()->syncWithoutDetaching($request->categories);
            }
            
            $supplier->name = $request->name;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->address = $request->address;
            $supplier->zip = $request->zip;
            $supplier->website = $request->website;
            $supplier->state_id = $request->state_id;
            $supplier->country_id = $request->country_id;
            $supplier->notes = $request->notes;

            $supplier->save();

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
