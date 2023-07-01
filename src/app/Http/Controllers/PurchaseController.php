<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Purchase;
use App\Http\Requests\ProductRequest;
use App\Models\SubCategory;
use App\Helpers\FunctionsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{

    private $staticDataHelper;

    private $dir = 'public/images/products';

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        $suppliers = $this->staticDataHelper->callSupliers();
        $brands = $this->staticDataHelper->callBrands();
        $categories = $this->staticDataHelper->loadCallCategories();
        return view('purchases.create', [
            'suppliers' => $suppliers,
            'brands' => $brands,
            'categories' => $categories
        ]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $file = isset($data['image']) ? $data['image'] : false;
            $price = $data['price'];
            $quantity = $data['quantity'];
            $subcategories = isset($data['subcategories']) && !empty($data['subcategories']) ? $data['subcategories'] : null;
            $brands = isset($data['brands']) && !empty($data['brands']) ? $data['brands'] : null;
            $category = $data['category_id'];
            $imagePath = Storage::url($this->dir);

            $totalPrice = FunctionsHelper::calculatedFinalPrice($price, $quantity);

            $purchase = Purchase::create([
                "name" => $data['name'],
                "supplier_id" => $data['supplier_id'],
                "quantity" => $quantity,
                "initial_quantity" => $quantity,
                "notes" => $data["notes"],
                "price" => $price,
                "code" => $data["code"],
                "status" => 'enabled',
                "total_price" => $totalPrice
            ]);

            if ($category) {
                $purchase->categories()->sync([$category]);
            }

            if ($subcategories !== null) {
                $purchase->subcategories()->sync($subcategories);
            }

            if ($brands !== null) {
                $purchase->brands()->sync($brands);
            }

            if ($file) {
                $hashed_image = Str::random(10) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);

                $purchase->images()->create([
                    'path' => $imagePath,
                    'name' => $hashed_image
                ]);
            }
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

        $brands = $this->staticDataHelper::callBrands();
        $suppliers = $this->staticDataHelper::callSupliers();
        $categories = $this->staticDataHelper::loadCallCategories();

        return view('purchases.edit', compact(
            'purchase',
            'relatedProductData',
            'suppliers',
            'brands',
            'suppliers',
            'categories'
        ));
    }

    public function update(Purchase $purchase, ProductRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $file = isset($data['image']) ? $data['image'] : false;
            $price = $data['price'];
            $quantity = $data['quantity'];
            $subcategories = isset($data['subcategories']) && !empty($data['subcategories']) ? $data['subcategories'] : null;
            $brands = isset($data['brands']) && !empty($data['brands']) ? $data['brands'] : null;
            $category = $data['category_id'];
            $imagePath = Storage::url($this->dir);

            if ($category) {
                $purchase->categories()->sync([$category]);
            }

            if ($subcategories !== null) {
                $purchase->subcategories()->sync($subcategories);
            }

            if ($brands !== null) {
                $purchase->brands()->sync($brands);
            }

            if ($file) {
                $hashed_image = Str::random(10) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);

                $purchase->images()->create([
                    'path' => $imagePath,
                    'name' => $hashed_image
                ]);
            }

            $totalPrice = FunctionsHelper::calculatedFinalPrice($price, $quantity);

            $purchase->update([
                "name" => $data['name'],
                "supplier_id" => $data['supplier_id'],
                "quantity" => $data['quantity'],
                "initial_quantity" => $data['quantity'],
                "notes" => $data["notes"],
                "price" => $price,
                "code" => $data["code"],
                "status" => 'enabled',
                "total_price" => $totalPrice
            ]);

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
        return view('purchases.preview', ['purchase' => $purchase]);
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
                    if ($requestedQuantity !== null && $requestedQuantity !== null) {
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
}
