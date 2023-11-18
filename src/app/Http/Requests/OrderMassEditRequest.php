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
            'order_ids' => 'required|array',
            'single_sold_price' => 'nullable|numeric|min:0',
            'sold_quantity' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'package_id' => 'nullable|string',
            'user_id' => 'nullable|string'
        ];
    }
}
