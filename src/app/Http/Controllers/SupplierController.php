<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Helpers\LoadStaticData;
use App\Models\SupplierImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{

    private $staticDataHelper;
    private $dir = 'public/images/suppliers';

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

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

    public function create()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries("countries");
        $categories = $this->staticDataHelper->loadCallCategories();

        return view('suppliers.create', ["countries" => $countries, "categories" => $categories]);
    }

    public function getState($countryId)
    {
        return response()->json(
            $this->staticDataHelper->callStatesAndCountries($countryId, 'states')
        );
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $file = isset($data['image']) ? $data['image'] : false;
            $categories = isset($data['categories']) ? $data['categories'] : false ;
            $imagePath = Storage::url($this->dir);

            $supplier = Supplier::create($data);

            if (isset($categories) && !empty($categories)) {
                $supplier->categories()->sync($categories);
            }

            if ($file) {
                $hashed_image = Str::random(10).'.'.$file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);

                $image = new SupplierImage([
                    'path' => $imagePath,
                    'name' => $hashed_image
                ]);

                $supplier->image()->save($image);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Supplier has not been created'], 500);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier has been created');
    }

    public function edit(Supplier $supplier)
    {
        $country = $supplier->country_id;

        $categories = $this->staticDataHelper->loadCallCategories();
        $states = $this->staticDataHelper->callStatesAndCountries($country, 'states');
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');

        $supplier = $supplier->load('image');

        return view('suppliers.edit', compact('supplier'), [
            'countries' => $countries,
            'states' => $states,
            'categories' => $categories,
            'related_categories' => $supplier->categories->pluck('id')->toArray()
        ]);
    }

    public function update(Supplier $supplier, SupplierRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $file = isset($data['image']) ? $data['image'] : false;
            $categories = isset($data['categories']) ? $data['categories'] : false ;
            $imagePath = Storage::url($this->dir);

            if (isset($categories) && !empty($categories)) {
                $supplier->categories()->sync($categories);
            }

            if($file) {
                $hashed_image = Str::random(10).'.'.$file->getClientOriginalExtension();
                $current_image = null;

                if($supplier->image) {
                    $current_image = $this->dir.DIRECTORY_SEPARATOR.$supplier->image->name;
                 
                    if(Storage::exists($current_image)) {
                        Storage::delete($current_image);
                    } 

                    Storage::putFileAs($this->dir, $file, $hashed_image);

                    $supplier->image->name = $hashed_image;
                    $supplier->image->path = $imagePath;
                    $supplier->image->save();
                } else {
                    Storage::putFileAs($this->dir, $file, $hashed_image);
                    
                    $image = new SupplierImage([
                        'path' => $imagePath,
                        'name' => $hashed_image
                    ]);

                    $supplier->image()->save($image);
                }            
            }

            $supplier->update($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }


        return redirect()->route('supplier.index')->with('success', 'Supplier has been updated');
    }

    public function delete(Supplier $supplier)
    {
        DB::beginTransaction();
        try {

            if ($supplier->purchases()->exists()) {
                return response()->json(['message' => 'Supplier has related purchases'], 500);
            }

            if($supplier->image) {
                $current_image = $this->dir.DIRECTORY_SEPARATOR.$supplier->image->name;
                
                if(Storage::exists($current_image)) {
                    Storage::delete($current_image);
                };
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

    public function detachCategory($id)
    {
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

    public function massEdit(Supplier $supplier){
        $categories = $supplier->categories()->get();
        $brands = $this->staticDataHelper->callBrands();
        return view('suppliers.mass_edit_purchases',['supplier' => $supplier,'categories' => $categories,'brands' => $brands]);
    }
}
