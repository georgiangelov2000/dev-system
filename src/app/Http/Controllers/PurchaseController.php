<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Models\SubCategory;
use App\Models\PurchasePayment;
use App\Helpers\FunctionsHelper;
use App\Http\Requests\PurchaseMassEditRequest;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    const INIT_STATUS = 2;

    private $staticDataHelper;

    private $helper;

    private $dir = 'public/images/products';

    private $statuses;

    private $service;

    public function __construct(
        LoadStaticData $staticDataHelper, 
        FunctionsHelper $helper,
        PurchaseService $service
    )
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
        $this->service = $service;
        $this->statuses = config('statuses.payment_statuses');
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->service->purchaseProcessing($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchase.index')->with('error', 'Purchase has not been created');
        }
        return redirect()->route('purchase.index')->with('success', 'Purchase has been created');
    }
    public function edit(Purchase $purchase)
    {        
        $orderAmounts = 0;

        if($purchase->orders()->exists()){
            $orderAmounts = $purchase->orders->sum('sold_quantity'); 
        }

        // Assuming $purchaseId is the ID you want to use in the query
        $paymentRecord = PurchasePayment::where('purchase_id', $purchase->id)->first();

        // Check if the payment record exists and is not null
        if ($paymentRecord) {
            $isEditable = $this->helper->statusValidation($paymentRecord->payment_status,$this->statuses) === 2; 
        }

        $purchase->order_amount = $orderAmounts;
        $purchase->is_editable = $isEditable;

        return view('purchases.edit',compact('purchase'));
    }

    public function update(Purchase $purchase, PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->service->purchaseProcessing($data, $purchase);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchase.create')->with('error', 'Product has not updated');
        }
        return redirect()->route('purchase.index')->with('success', 'Product has been updated');
    }

    public function preview(Purchase $purchase)
    {
        $purchase->load('brands', 'categories', 'supplier:id,name', 'subcategories', 'images');
        return view('purchases.preview', compact('purchase'));
    }

    public function massEditUpdate(PurchaseMassEditRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            foreach ($validated['purchases'] as $purchaseId) {
                $this->service->purchaseMassEditProcessing($validated, $purchaseId);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Purchases has not been updated'], 500);
        }

        return response()->json(['message' => 'Purchases has been updated'], 200);
    }
    public function fetchRelatedProductData($purchaseModel)
    {
        $purchaseModel->load('categories:id', 'subcategories:id', 'brands:id');

        $categorySubCategories = SubCategory::select('id', 'name')
            ->whereIn('category_id', $purchaseModel->categories->pluck('id'))
            ->get()
            ->toArray();

        return [
            'categorySubCategories' => $categorySubCategories,
            'purchaseCategory' => $purchaseModel->categories->pluck('id')->first(),
            'purchaseSubCategories' => $purchaseModel->subcategories->pluck('id')->toArray(),
            'purchaseBrands' => $purchaseModel->brands->pluck('id')->toArray(),
        ];
    }

    public function delete(Purchase $purchase)
    {
        DB::beginTransaction();

        try {
            $imagePath = str_replace('/storage', '', $purchase->image_path);

            // Check if the image path exists and delete it
            if ($purchase->image_path && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $purchase->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Purchase has not been deleted'], 500);
        }
        return response()->json(['message' => 'Purchase has been deleted'], 200);
    }

    public function orders(Purchase $purchase)
    {
        return view('purchases.orders', compact('purchase'));
    }
}
