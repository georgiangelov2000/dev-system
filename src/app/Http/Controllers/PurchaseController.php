<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Models\SubCategory;
use App\Helpers\FunctionsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{

    private $staticDataHelper;

    private $helper;

    private $dir = 'public/images/products';

    public function __construct(LoadStaticData $staticDataHelper, FunctionsHelper $helper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        $data = $this->loadStaticData();
        return view('purchases.create', $data);
    }

    public function store(PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->purchaseProcessing($data);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchase.index')->with('error', 'Purchase has not been created');
        }
        return redirect()->route('purchase.index')->with('success', 'Purchase has been created');
    }
    public function edit(Purchase $purchase)
    {
        $relatedProductData = $this->fetchRelatedProductData($purchase);
        $data = $this->loadStaticData();
        $is_available = $purchase->payment === null ? true : false;

        return view('purchases.edit', compact('purchase', 'relatedProductData','is_available'), $data);
    }

    public function update(Purchase $purchase, PurchaseRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $data = $request->validated();
    
            $this->purchaseProcessing($data,$purchase);

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

    public function massEditUpdate(Request $request)
    {
        $validated = $request->validate([
            'purchase_ids' => 'required|array',
            'quantity' => 'nullable|integer',
            'price' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'brand_ids' => 'nullable|array',
            'sub_category_ids' => 'nullable|array'
        ]);

        $purchaseIds = $validated['purchase_ids'];
        $requestedQuantity = $validated['quantity'] ?? null;
        $requestedPrice = $validated['price'] ?? null;
        $requestedCategoryId = $validated['category_id'] ?? null;
        $brandIds = $validated['brand_ids'] ?? [];
        $subCategoryIds = $validated['sub_category_ids'] ?? [];

        DB::beginTransaction();

        try {
            foreach ($purchaseIds as $purchaseId) {
                $purchase = Purchase::find($purchaseId);

                if ($purchase) {
                    if ($requestedQuantity !== null && $requestedPrice !== null) {
                        $purchase->quantity = $requestedQuantity;
                        $purchase->initial_quantity = $requestedQuantity;
                        $purchase->price = $requestedPrice;
                    } elseif ($requestedQuantity !== null) {
                        $purchase->quantity = $requestedQuantity;
                        $purchase->initial_quantity = $requestedQuantity;
                    } elseif ($requestedPrice !== null) {
                        $purchase->price = $requestedPrice;
                    }

                    if ($requestedCategoryId !== null) {
                        $purchase->categories()->sync([$requestedCategoryId]);
                    }
                    if (!empty($subCategoryIds)) {
                        $purchase->subcategories()->sync($subCategoryIds);
                    }
                    if (!empty($brandIds)) {
                        $purchase->brands()->sync($brandIds);
                    }

                    $purchase->total_price = FunctionsHelper::calculatedFinalPrice($purchase->price, $purchase->quantity);

                    $orders_quantity = $purchase->orders->sum('sold_quantity');

                    $final_purchase_quantity = ($purchase->initial_quantity - $orders_quantity);

                    $purchase->quantity = $final_purchase_quantity;

                    $purchase->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Purchases has been updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Purchases has not been updated'], 500);
        }
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
            if ($purchase->payment()->exists()) {
                return response()->json(['message' => 'Payment already exists for this purchase'], 500);
            }

            if (!empty($purchase->images)) {
                $image_names = $purchase->images()->pluck('name');

                foreach ($image_names as $key => $images) {
                    Storage::delete($this->dir . DIRECTORY_SEPARATOR . $images);
                }
            }

            $purchase->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Purchase has not been deleted'], 500);
        }
        return response()->json(['message' => 'Purchase has been deleted'], 200);
    }

    public function deleteGalleryImage(Purchase $purchase, Request $request)
    {
        DB::beginTransaction();

        try {
            $image = $purchase->images()->find($request->id);
            if ($image) {
                Storage::delete($this->dir . DIRECTORY_SEPARATOR . $image->name);
                $image->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Image has not been deleted'], 500);
        }

        return response()->json(['message' => 'Image has been deleted'], 200);
    }

    public function orders(Purchase $purchase)
    {
        return view('purchases.orders', compact('purchase'));
    }

    // Private methods

    private function loadStaticData()
    {
        $suppliers = $this->staticDataHelper->callSupliers();
        $brands = $this->staticDataHelper->callBrands();
        $categories = $this->staticDataHelper->loadCallCategories();

        return [
            'suppliers' => $suppliers,
            'brands' => $brands,
            'categories' => $categories
        ];
    }

    private function purchaseProcessing(array $data, $purchase = null)
    {
        $prices = null;

        // Check if $data['image'] exists and set it to $file
        $file = $data['image'] ?? null;

        // Check if $data['subcategories'] and $data['brands'] exist and are not empty
        $subCategories = $data['subcategories'] ?? null;
        $brands = $data['brands'] ?? null;

        if ($subCategories !== null) {
            $subCategories = $this->getNonEmptyArray($subCategories);
        }

        if ($brands !== null) {
            $brands = $this->getNonEmptyArray($brands);
        }

        $purchase = $purchase ? $purchase : new Purchase;

        $isNewPurchase = !$purchase->exists; // Check if it's a new purchase
        $status = $isNewPurchase ? $this->checkForValidStatus(6) : $this->checkForValidStatus($purchase->status);

        // Update purchase or create Purchase;
        $purchase->name = $data['name'];
        $purchase->supplier_id = $data['supplier_id'];
        $purchase->notes = $data['notes'] ?? '';

        if( ($purchase && $status === 6) || $isNewPurchase){
            // Calculate prices
            $prices = $this->calculatePrices($data['price'], $data['discount_percent'], $data['quantity']);        
            
            // Update quantities
            $purchase->quantity = $data['quantity'];
            $purchase->initial_quantity = $data['quantity'];

            // Update prices
            $purchase->price = $data['price'];
            $purchase->total_price = $prices['total_price'];
            $purchase->original_price = $prices['original_price'];
            $purchase->discount_price = $prices['discount_price'];

            // Update code
            $purchase->code = $data['code'];

            // Update dates
            $purchase->expected_date_of_payment = now()->parse($data['expected_date_of_payment']);
            $purchase->delivery_date = now()->parse($data['delivery_date']);
            $purchase->status = $status;
        }

        $purchase->save();
        
        if ($data['category_id']) {
            $purchase->categories()->sync([$data['category_id']]);
        }

        if ($subCategories) {
            $purchase->subcategories()->sync($subCategories);
        }

        if ($brands) {
            $purchase->brands()->sync($brands);
        }
        
        if ($file) {
            $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            Storage::putFileAs($this->dir, $file, $hashed_image);

            $purchase->images()->create([
                'path' => $this->getImagePath(),
                'name' => $hashed_image,
            ]);
        }
        
    }

    private function calculatePrices($price, $discount, $quantity): array
    {

        $discountPrice = $this->helper->calculatedDiscountPrice($price, $discount);
        $totalPrice = $this->helper->calculatedFinalPrice($discountPrice, $quantity);
        $originalPrice = $this->helper->calculatedFinalPrice($price, $quantity);

        return [
            'discount_price' => $discountPrice,
            'total_price' => $totalPrice,
            'original_price' => $originalPrice
        ];
    }

    private function getNonEmptyArray($value): ?array
    {
        return is_array($value) ? array_filter($value) : null;
    }

    private function getImagePath(): string
    {
        return Storage::url($this->dir);
    }
    
    private function checkForValidStatus (int $status):int {
        $statuses = config('statuses.purchase_statuses');
        return array_key_exists($status,$statuses) ? $status : null;
    }
}
