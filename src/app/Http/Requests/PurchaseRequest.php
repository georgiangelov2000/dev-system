<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            "image" => "nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048",
            "name" => "required|string",
            "quantity" => "required|integer|min:0",
            "price" => "required|numeric|min:0",
            "code" => "required|string",
            "supplier_id" => "required|integer|not_in:0",
            "category_id" => "required|integer|not_in:0",
            "subcategories" => "nullable|array",
            "notes" => 'nullable|string',
            "brands" => "nullable|array",
            'expected_date_of_payment' => 'required|date',
            'discount_percent' => 'required|numeric|min:0'
        ];
    }

}
