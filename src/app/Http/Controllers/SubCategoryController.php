<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\FunctionsHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\Log as LogModel;

class SubCategoryController extends Controller
{
    private $helper;

    public function __construct(FunctionsHelper $helper) {
        $this->helper = $helper;
    }

    public function index(){
        return view('subcategories.index');
    }

    public function store(SubCategoryRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
    
        try {
            $subcategory = SubCategory::create([
                'name' => $data['name'],
            ]);

            if(!$subcategory) {
                throw new \Exception("Subcategory has not been created");
            }
    
            $log = $this->helper->logData(
                'store_sub_category',
                'store_sub_category_action',
                $subcategory->name,
                Auth::user(),
                now(),
            );

            LogModel::create($log);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error' => 'Subcategory has not been created'], 500);
        }
        return response()->json(['message' => 'Subcategory has been created'], 200);
    }

    public function edit(SubCategory $subcategory) {
        return response()->json($subcategory);
    }

    public function update(SubCategory $subcategory, SubCategoryRequest $request){
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $subcategory->update([
                'name' => $data['name']
            ]);
            
            $log = $this->helper->logData(
                'update_sub_category',
                'update_sub_category_action',
                $subcategory->name,
                Auth::user(),
                now(),
            );

            LogModel::create($log);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['message' => 'Subcategory has not been updated'], 500);
        }
        return response()->json(['message' => 'Subcategory has been updated'], 200);
    }

    public function delete(SubCategory $subcategory) {
        DB::beginTransaction();

        try {

            if($subcategory->purchases->isNotEmpty()) {
                throw new \Exception("Subcategory has been assigned to purchases");
            }

            $name = $subcategory->name;
            $subcategory->delete();

            // Log the deletion action
            $log = $this->helper->logData(
                'delete_sub_category',
                'delete_sub_category_action',
                $name,
                Auth::user(),
                now(),
            );
            
            // Log the deletion action
            LogModel::create($log);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Subcategory has been deleted'], 200);
    }

}
