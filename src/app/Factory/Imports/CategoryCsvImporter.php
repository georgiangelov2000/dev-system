<?php

namespace App\Factory\Imports;

use App\Factory\CsvImporter;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Validator;

final class CategoryCsvImporter extends CsvImporter
{
    /**
     * Array of validation rules for the category.
     *
     * @var array
     */
    private $keys = [
        'name' => 'required|string',
        'description' => 'nullable|string',
        'sub_categories' => "nullable|array",
    ];

    /**
     * Performs validation and creates providers from the provided data.
     *
     * @param array $data
     * @return array
     */
    public function initValidation(array $data): array
    {
        $createdCategories = [];

        foreach ($data as $key => $value) {

            // Create array of subcategories if they are exist
            if (array_key_exists('sub_categories', $value)) {
                $value['sub_categories'] = trim($value['sub_categories']);

                // Use regular expression to match the pattern integer&integer
                if (preg_match('/^\d+&\d+$/', $value['sub_categories'])) {
                    $value['sub_categories'] = explode('&', $value['sub_categories']);
                } elseif (ctype_digit($value['sub_categories'])) {
                    $value['sub_categories'] = [$value['sub_categories']];
                } else {
                    return ['error' => "Please use the format 'integer&integer' or a single integer."];
                }
            }

            // Performs validation and creates a new provider
            $validator = Validator::make($value, $this->keys);

            // Throw errors
            if ($validator->fails()) {
                return ['error' => 'Data has not been inserted'];
            }

            // Create category
            $category = Category::create($validator->validated());

            if (array_key_exists('sub_categories', $value)) {
                // Attach the selected subcategories to the Category.
                $this->categoryService()->attachSubCategories(
                    $value['sub_categories'],
                    $category->id
                );
            }

            $createdCategories[] = $category;
        }
        return $createdCategories;
    }

    private function categoryService()
    {
        return new CategoryService();
    }
}
