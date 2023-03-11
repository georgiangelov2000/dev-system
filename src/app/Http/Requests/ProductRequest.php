<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "image" => "image|mimes:jpeg,png,jpg,gif|max:2048",
            "name" => "required|string",
            "supplier_id" => "required|string",
            "categories" => "array",
            "subcategories" => "array",
            "quantity" => "required|integer",
            "price" => "required|integer",
            "discount_price" => "required|integer",
            "discount_percent" => "required|integer",
            "code" => "required|string",
            "start_date_discount" => "date|required",
            "end_date_discount" => "date|required",
            "notes" => 'string'
        ];
    }
}
