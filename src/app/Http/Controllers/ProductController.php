<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Models\SubCategory;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('purchases.index',[
            'suppliers' => $this->staticDataHelper->callSupliers(),
            'categories' => $this->staticDataHelper->loadCallCategories(),
            'brands' => $this->staticDataHelper->callBrands()
        ]);
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
            $price = $data['price'];
            $quantity = $data['quantity'];

            $active = $quantity > 0 ? $active = 'enabled' : 'disabled';
            $totalPrice = $this->totalPrice($price,$quantity);
            
            $product = Product::create([
                "name" => $data['name'],
                "supplier_id" => $data['supplier_id'],
                "quantity" => $quantity,
                "initial_quantity" => $quantity,
                "notes" => $data["notes"],
                "price" => $price,
                "code" => $data["code"],
                "status" => $active,
                "total_price" => $totalPrice
            ]);

            if ($product) {
                $productService = new ProductService($product);

                if (isset($data['image'])) {
                    $productService->imageUploader($data['image']);
                }

                $productService->attachProductCategory($data['category_id']);

                if (isset($data['subcategories']) && count($data['subcategories'])) {
                    $productService->attachProductSubcategories($data['subcategories']);
                }

                if (isset($data['brands']) && count($data['brands'])) {
                    $productService->attachProductBrands($data['brands']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
        }
        return redirect()->route('purchase.index')->with('success', 'Product has been created');
    }

    public function update(Product $product, ProductRequest $request)
    {
        $productService = new ProductService($product);
        $data = $request->validated();

        DB::beginTransaction();
        try {

            if (isset($data['image'])) {
                $productService->imageUploader($data['image']);
            }

            $productService->attachProductCategory($data['category_id']);

            if (isset($data['subcategories']) && count($data['subcategories'])) {
                $productService->attachProductSubcategories( $data['subcategories'] );
            }

            if (isset($data['brands']) && count($data['brands'])) {
                $productService->attachProductBrands($data['brands']);
            }

            $active = $data['quantity'] > 0 ? $active = 'enabled' : 'disabled';
            $totalPrice = $this->totalPrice($data['price'],$data['quantity']);

            $product->update([
                "name" => $data['name'],
                "supplier_id" => $data['supplier_id'],
                "quantity" => $data['quantity'],
                "initial_quantity" => $data['quantity'],
                "notes" => $data["notes"],
                "price" => $data['price'],
                "code" => $data["code"],
                "status" => $active,
                "total_price" => $totalPrice
            ]);

            DB::commit();

            Log::info('Product has been updated');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->route('purchase.create')->with('error', 'Product has not updated');
        }
        return redirect()->route('purchase.index')->with('success', 'Product has been updated');
    }

    public function preview(Product $product){
        $product->load('brands', 'categories', 'supplier:id,name', 'subcategories','images');
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
            $productImages = $product->images() ? $product->images()->pluck('name') : null;

            if (count($productImages)) {
                foreach ($productImages as $key => $value) {
                    $imagePath = storage_path('app/public/images/products/' . $value);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
            $product->categories()->detach();
            $product->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Log::info($e->getMessage());
            return response()->json(['message' => 'Failed to delete product'], 500);
        }
        return response()->json(['message' => 'Product has been deleted'], 200);
    }

    public function deleteGalleryImage(Product $product, Request $request)
    {
        DB::beginTransaction();

        try {
            $imageId = $request->image_id;
            $image = $product->images()->find($imageId);
            if ($image) {
                unlink(storage_path('app/public/images/products/'.$image->name));
                $image->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Image has not been deleted'], 500);
        }

        return response()->json(['message' => 'Image has been deleted'], 200);
    }

    private function totalPrice($single_price, $quantity){
        $finalPrice = 0;

        if ( ($single_price && $quantity) && (is_numeric($single_price) && is_numeric($quantity)) ) 
            {
                $finalPrice = ($single_price * $quantity);
            } else {
                $finalPrice  = $single_price;
            }

        return $finalPrice;
    }
}
