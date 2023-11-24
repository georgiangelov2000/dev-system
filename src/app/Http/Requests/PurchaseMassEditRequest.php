<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseMassEditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'purchase_ids' => 'required|array',
            'quantity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'categories' => 'nullable|integer',
            'brands' => 'nullable|array',
            'subcategories' => 'nullable|array',
            'discount_percent' => 'nullable|integer',
            'expected_date_of_payment' => 'nullable|date',
            'expected_delivery_date' => 'nullable|date'
        ];
    }
}
