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
            'purchases' => 'required|array',
            'quantity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|integer',
            'brands' => 'nullable|array',
            'subcategories' => 'nullable|array',
            'discount_percent' => 'nullable|integer'
        ];
    }
}
