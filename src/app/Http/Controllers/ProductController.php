<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\ProductSubcategory;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    private $staticDataHelper;
    private $storage_static_files = 'public/images/products';

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function index()
    {
        return view('products.index');
    }

    public function create()
    {
        $suppliers = $this->staticDataHelper->callSupliers();
        $brands = $this->staticDataHelper->callBrands();
        return view('products.create', ['suppliers' => $suppliers, 'brands' => $brands]);
    }


    public function edit(Product $product)
    {
        $productService = new ProductService($product);

        $relatedRecords  = $productService->getEditData();
        $brands = $this->staticDataHelper::callBrands();
        $suppliers = $this->staticDataHelper::callSupliers();
        return view('products.edit', compact('product', 'relatedRecords', 'suppliers', 'brands'));
    }

    public function update(Product $product, ProductRequest $request)
    {
        $productService = new ProductService($product);

        $data = $request->validated();

        DB::beginTransaction();
        try {

            if ($request->hasFile('image')) {
                $productService->imageUploader($request->file('image'));
            }

            $productService->attachProductCategory($data['category_id']);

            if (isset($data['subcategories']) && count($data['subcategories'])) {
                $productService->attachProductSubcategories( $data['subcategories'] );
            }

            if (isset($data['brands']) && count($data['brands'])) {
                $productService->attachProductBrands($data['brands']);
            }

            DB::commit();

            Log::info('Product has been updated');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            Log::error($e->getMessage());
        }
        return redirect()->route('product.index')->with('success', 'Product has been updated');
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {

            $active = $data['quantity'] > 0 ? $active = 'enabled' : 'disabled';
            $total_price = $data['price'] * $data['quantity'];
            $formatted_total_price = number_format($total_price, 8, '.', ',');

            $product = Product::create([
                "name" => $data['name'],
                "supplier_id" => $data['supplier_id'],
                "quantity" => $data['quantity'],
                "notes" => $data["notes"],
                "price" => $data["price"],
                "code" => $data["code"],
                "status" => $active,
                "total_price" => $formatted_total_price
            ]);

            if ($product) {

                $image = $request->file('image');

                if ($image) {
                    $hashedImage = Str::random(10) . '.' . $image->getClientOriginalExtension();

                    $createdProductImage = $this->createProductImage(
                        $product->id,
                        config('app.url') . '/storage/images/products/',
                        $hashedImage
                    );

                    if ($createdProductImage) {
                        if (!Storage::exists($this->storage_static_files)) {
                            Storage::makeDirectory($this->storage_static_files);
                        }
                        Storage::putFileAs($this->storage_static_files, $image, $hashedImage);
                    }
                }

                $productService = new ProductService($product);

                $productService->attachProductCategory($data['category_id']);

                if (isset($data['subcategories']) && count($data['subcategories'])) {
                    $productService->attachProductSubcategories($data['subcategories']);
                }

                if (isset($data['brands']) && count($data['brands'])) {
                    $productService->attachProductBrands($data['brands']);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
        return redirect()->route('product.index')->with('success', 'Product has been created');
    }

    public function delete(Product $product)
    {

        DB::beginTransaction();

        try {

            $productImages = $product->images() ? $product->images()->get() : null;

            if (count($productImages)) {
                foreach ($productImages as $key => $value) {
                    $imagePath = storage_path('app/public/images/products/' . $value->name);

                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

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

    private function createProductImage($productId, $path, $name)
    {
        $image = ProductImage::create([
            'product_id' => $productId,
            'path' => $path,
            'name' => $name
        ]);

        return $image;
    }
}
