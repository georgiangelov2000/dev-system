<?php
namespace App\Services;
use App\Models\SubCategory;


class CategoryService {

    public function attachSubCategories(array $subCategories, int $category){

        if(isset($subCategories) && !empty($subCategories)) {
            foreach ($subCategories as $key => $subCategoryId) {
                $subCategory = SubCategory::findOrFail($subCategoryId);
                $subCategory->category()->associate($category);
                $subCategory->save();
            }
        }

    }

}