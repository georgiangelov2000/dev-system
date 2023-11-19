<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderMassEditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids' => 'required|array',
            'single_sold_price' => 'nullable|numeric|min:0',
            'sold_quantity' => 'nullable|integer|min:0',
            'discount_percent' => 'nullable|integer|min:0',
            'expected_delivery_date' => 'nullable|date',
            'expected_date_of_payment' => 'nullable|date',
            'package_id' => 'nullable|string',
            'user_id' => 'nullable|string'
        ];
    }
}
