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
            'customer_id' => 'required|integer',
            'date_of_sale' => 'required|date',
            'tracking_number' => 'required|string',
            
            'purchase_id' => 'required',
            'purchase_id.*' => 'numeric',
        
            'sold_quantity' => 'required',
            'sold_quantity.*' => 'numeric|min:1',
        
            'single_sold_price' => 'required',
            'single_sold_price.*' => 'numeric|min:0',
        
            'discount_percent' => 'required',
            'discount_percent.*' => 'numeric|min:0',        
        ];
    }

    public function __get($key)
    {
        return $this->validated()[$key] ?? null;
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product ID field is required.',
            'product_id.array' => 'The product ID must be an array.',
            'product_id.*.required' => 'Product ID field is required.',
            'product_id.*.numeric' => 'Product ID must be a numeric value.',
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
