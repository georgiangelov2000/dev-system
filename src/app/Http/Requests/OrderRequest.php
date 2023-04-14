<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => "required|integer",
            "date_of_sale" => "required|date",
            "status" => "required|integer",
            'product_id' => ["array", "nullable"],
            'invoice_number' => ["array", "nullable"],
            'sold_quantity' => ["array", "nullable"],
            'single_sold_price' => ["array", "nullable"],
            "total_sold_price" => ["array", "nullable"],
            "discount_percent" => ["array", "nullable"],
            'product_id.*' => 'required',
            'invoice_number.*' => 'required',
            'sold_quantity.*' => 'required',
            'single_sold_price.*' => 'required',
            "total_sold_price.*" => 'required',
            "discount_percent.*" => 'required',
        ];
    }
}
