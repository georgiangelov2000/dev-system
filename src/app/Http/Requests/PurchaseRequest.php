<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $method = $this->method(); // Get the request method (POST, PUT, etc.)
        $purchase = $this->purchase ?? null;
        $hasPaymentRelation = $purchase ? $purchase->payment : false;

        $rules = [
            "image" => "nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048",
            "name" => "required|string",
            "code" => ($method == 'POST' || ($method == 'PUT' && !$hasPaymentRelation)) ? 'required|string' : 'nullable|string',
            "supplier_id" => "required|integer|not_in:0",
            "category_id" => "required|integer|not_in:0",
            "subcategories" => "nullable|array",
            "notes" => "nullable|string",
            "brands" => "nullable|array",
            "delivery_date" => ($method == 'POST' || ($method == 'PUT' && !$hasPaymentRelation)) ? 'required|date' : 'nullable|date',
            'expected_date_of_payment' => ($method == 'POST' || ($method == 'PUT' && !$hasPaymentRelation)) ? 'required|date' : 'nullable|date',
            'discount_percent' => ($method == 'POST' || ($method == 'PUT' && !$hasPaymentRelation)) ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'price' => ($method == 'POST' || ($method == 'PUT' && !$hasPaymentRelation)) ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'quantity' => ($method == 'POST' || ($method == 'PUT' && !$hasPaymentRelation)) ? 'required|integer|min:0' : 'nullable|integer|min:0',
        ];

        return $rules;
    }
}
