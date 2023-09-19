<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function rules()
    {
        $method = $this->method();
        $order = $this->order ?? null;
        $statusIsAllowed = [];

        if($order) {
            $statusIsAllowed = in_array($order->status, [1, 2, 3, 4, 5]);
        }
        
        $rules = [
            'customer_id' => 'required|string',
            'user_id' => 'required|string',
            'package_id' => 'nullable',
            'purchase_id' => 'required',
            'purchase_id.*' => 'numeric',
            'tracking_number' => $statusIsAllowed && $method === 'PUT' ? 'nullable|string' : 'required|string',
            'sold_quantity' => $statusIsAllowed && $method === 'PUT' ? 'nullable' : 'required',
            'sold_quantity.*' => $statusIsAllowed && $method === 'PUT' ? 'nullable|numeric|min:1' : 'required|numeric|min:1',
            'single_sold_price' => $statusIsAllowed && $method === 'PUT' ? 'nullable' : 'required',
            'single_sold_price.*' => $statusIsAllowed && $method === 'PUT' ? 'nullable|numeric|min:0' : 'required|numeric|min:1',
            'discount_percent' => $statusIsAllowed && $method === 'PUT' ? 'nullable' : 'required',
            'discount_percent.*' => $statusIsAllowed && $method === 'PUT' ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'date_of_sale' => $statusIsAllowed && $method === 'PUT' ? 'nullable' : 'required|date',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'purchase_id.required' => 'The product ID field is required.',
            'purchase_id.array' => 'The product ID must be an array.',
            'purchase_id.*.required' => 'Product ID field is required.',
            'purchase_id.*.numeric' => 'Product ID must be a numeric value.',
            'sold_quantity.required' => 'The sold quantity field is required.',
            'sold_quantity.array' => 'The sold quantity must be an array.',
            'sold_quantity.*.required' => 'Sold quantity field is required.',
            'sold_quantity.*.numeric' => 'Sold quantity must be a numeric value.',
            'single_sold_price.required' => 'The single sold price field is required.',
            'single_sold_price.array' => 'The single sold price must be an array.',
            'single_sold_price.*.required' => 'Single sold price field is required.',
            'single_sold_price.*.numeric' => 'Single sold price must be a numeric value.',
            'discount_percent.required' => 'The discount percent field is required.',
            'discount_percent.array' => 'The discount percent must be an array.',
            'discount_percent.*.required' => 'Discount percent field is required.',
            'discount_percent.*.numeric' => 'Discount percent must be a numeric value.',
        ];
    }
}
