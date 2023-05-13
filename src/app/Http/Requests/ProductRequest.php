<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            "image" => "nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048",
            "name" => "required|string",
            "quantity" => "required|integer",
            "price" => ['required', function ($attribute, $value, $fail) {
                    if (is_int($value)) {
                        $value = number_format($value / 100, 2, '.', '');
                    } else if (is_numeric($value) && strpos($value, '.') === false) {
                        $value = number_format($value, 2, '.', '');
                    }
                    if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                        return $fail($attribute . ' is not a valid price format.');
                    }
                }],
            "code" => "required|string",
            "supplier_id" => "required|integer|not_in:0",
            "category_id" => "required|integer|not_in:0",
            "subcategories" => "array",
            "notes" => 'nullable|string',
            "brands" => "array"
        ];
    }

}
