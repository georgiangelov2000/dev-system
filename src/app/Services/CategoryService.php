<?php

namespace App\Services;
use App\Models\Category;

class CategoryService
{
    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function attachSubCategories($subCategories)
    {
        if (!is_array($subCategories)) {
            throw new \InvalidArgumentException('Subcategories must be provided as an array.');
        }

        $category = Category::findOrFail($this->category->id);
        
        $category->subCategories()->sync($this->convertArrayValuesToNumeric($subCategories));
    }

    private function convertArrayValuesToNumeric($array):array {
        return array_map('intval',$array);
    }

}
