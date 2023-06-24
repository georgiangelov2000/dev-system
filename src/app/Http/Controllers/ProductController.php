<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Models\SubCategory;
use App\Helpers\FunctionsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
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

            $product = Product::create([
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
                $product->categories()->sync([$category]);
            }

            if ($subcategories !== null) {
                $product->subcategories()->sync($subcategories);
            }

            if ($brands !== null) {
                $product->brands()->sync($brands);
            }

            if ($file) {
                $hashed_image = Str::random(10) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);

                $product->images()->create([
                    'path' => $imagePath,
                    'name' => $hashed_image
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->route('purchase.index')->with('error', 'Product has not been created');
        }
        return redirect()->route('purchase.index')->with('success', 'Product has been created');
    }

    public function update(Product $product, ProductRequest $request)
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
                $product->categories()->sync([$category]);
            }

            if ($subcategories !== null) {
                $product->subcategories()->sync($subcategories);
            }

            if ($brands !== null) {
                $product->brands()->sync($brands);
            }

            if ($file) {
                $hashed_image = Str::random(10) . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs($this->dir, $file, $hashed_image);

                $product->images()->create([
                    'path' => $imagePath,
                    'name' => $hashed_image
                ]);
            }

            $totalPrice = FunctionsHelper::calculatedFinalPrice($price, $quantity);

            $product->update([
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
            Log::error($e->getMessage());
            return redirect()->route('purchase.create')->with('error', 'Product has not updated');
        }
        return redirect()->route('purchase.index')->with('success', 'Product has been updated');
    }

    public function preview(Product $product)
    {
        $product->load('brands', 'categories', 'supplier:id,name', 'subcategories', 'images');
        return view('purchases.preview', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        $relatedProductData = $this->fetchRelatedProductData($product);

        $brands = $this->staticDataHelper::callBrands();
        $suppliers = $this->staticDataHelper::callSupliers();
        $categories = $this->staticDataHelper::loadCallCategories();

        return view('purchases.edit', compact(
            'product',
            'relatedProductData',
            'suppliers',
            'brands',
            'suppliers',
            'categories'
        ));
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

        $productIds = $validated['purchase_ids'];
        $requestedQuantity = $validated['quantity'] ?? null;
        $requestedPrice = $validated['price'] ?? null;
        $requestedCategoryId = $validated['category_id'] ?? null;
        $brandIds = $validated['brand_ids'] ?? [];
        $subCategoryIds = $validated['sub_category_ids'] ?? [];

        DB::beginTransaction();

        try {
            foreach ($productIds as $productId) {
                $product = Product::find($productId);

                if ($product) {
                    if ($requestedQuantity !== null && $requestedQuantity !== null) {
                        $product->quantity = $requestedQuantity;
                        $product->initial_quantity = $requestedQuantity;
                        $product->price = $requestedPrice;
                    } elseif ($requestedQuantity !== null) {
                        $product->quantity = $requestedQuantity;
                        $product->initial_quantity = $requestedQuantity;
                    } elseif ($requestedPrice !== null) {
                        $product->price = $requestedPrice;
                    }

                    if ($requestedCategoryId !== null) {
                        $product->categories()->sync([$requestedCategoryId]);
                    }
                    if (!empty($subCategoryIds)) {
                        $product->subcategories()->sync($subCategoryIds);
                    }
                    if (!empty($brandIds)) {
                        $product->brands()->sync($brandIds);
                    }

                    $product->total_price = FunctionsHelper::calculatedFinalPrice($product->price, $product->quantity);

                    $orders_quantity = $product->orders->sum('sold_quantity');

                    $final_product_quantity = ($product->initial_quantity - $orders_quantity);
                    
                    $product->quantity = $final_product_quantity;

                    $product->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Purchases has been updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json(['message' => 'Purchases has not been updated'], 500);
        }
    }
    public function fetchRelatedProductData($productModel)
    {
        $productModel->load('categories:id', 'subcategories:id', 'brands:id');

        $categorySubCategories = SubCategory::select('id', 'name')
            ->whereIn('category_id', $productModel->categories->pluck('id'))
            ->get()
            ->toArray();

        return [
            'categorySubCategories' => $categorySubCategories,
            'productCategory' => $productModel->categories->pluck('id')->first(),
            'productSubCategories' => $productModel->subcategories->pluck('id')->toArray(),
            'productBrands' => $productModel->brands->pluck('id')->toArray(),
        ];
    }

    public function delete(Product $product)
    {
        DB::beginTransaction();

        try {

            if (!empty($product->images)) {
                $image_names = $product->images()->pluck('name');

                foreach ($image_names as $key => $images) {
                    Storage::delete($this->dir . DIRECTORY_SEPARATOR . $images);
                }
            }

            $product->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Product has not been deleted'], 500);
        }
        return response()->json(['message' => 'Product has been deleted'], 200);
    }

    public function deleteGalleryImage(Product $product, Request $request)
    {
        DB::beginTransaction();

        try {
            $image = $product->images()->find($request->id);
            if ($image) {
                Storage::delete($this->dir . DIRECTORY_SEPARATOR . $image->name);
                $image->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Image has not been deleted'], 500);
        }

        return response()->json(['message' => 'Image has been deleted'], 200);
    }

    public function orders(Product $product)
    {
        return view('purchases.orders', compact('product'));
    }
}
