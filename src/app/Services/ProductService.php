<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductService
{

    protected $product;

    private $storage_static_files = 'public/images/products';

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getEditData()
    {
        $product = $this->product
            ->with('categories:id,name', 'subcategories:id,name', 'brands:id,name')
            ->firstOrFail();
            
        $result = [
            'supplier' => $product->supplier->first()->id,
            'category' => $product->categories->pluck('id')->first(),
            'brands' => $product->brands->pluck('id')->toArray(),
            'sub_categories' => json_encode($product->subcategories->pluck('id')->toArray())
        ];

        return $result;
    }

    public function imageUploader($file)
    {
        $hashedImage = Str::random(10) . '.' . $file->getClientOriginalExtension();

        $image = $this->product->images()->create([
            'path' => config('app.url') . '/storage/images/products/',
            'name' => $hashedImage,
        ]);

        $storedFile = $file->storeAs($this->storage_static_files, $hashedImage);

        return $storedFile ? $image : false;
    }

    public function attachProductCategory($category)
    {
        $product = Product::findOrFail($this->product->id);
        
        $product->categories()->sync([$category]);
    }

    public function attachProductSubcategories($subCategories)
    {
        $product = Product::findOrFail($this->product->id);
        $product->subcategories()->sync($this->convertArrayValuesToNumeric($subCategories));
    }

    public function attachProductBrands($brands)
    {
        $product = Product::findOrFail($this->product->id);
        $product->brands()->sync($this->convertArrayValuesToNumeric($brands));
    }

    private function convertArrayValuesToNumeric($array):array {
        return array_map('intval',$array);
    }
}
