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
        $this->product
        ->subcategories()
        ->sync($this->convertArrayValuesToNumeric($subCategories));
    }

    public function attachProductBrands($brands)
    {
        $this->product
        ->brands()
        ->sync($this->convertArrayValuesToNumeric($brands));
    }

    private function convertArrayValuesToNumeric($array):array {
        return array_map('intval',$array);
    }

}
