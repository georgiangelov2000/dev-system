<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'customer_id' => 'required|string',
            'user_id' => 'required|string',
            'package_id' => 'nullable',
        ];
        
        if($this->isPaymentRequired() || !$this->order) {
            $rules['purchase_id.*'] = 'required|string';
            $rules['tracking_number.*'] = 'required|string';
            $rules['sold_quantity.*'] = 'required|integer|min:1';
            $rules['single_sold_price.*'] = 'required|numeric|min:1';
            $rules['discount_percent.*'] = 'required|integer';
            $rules['date_of_sale'] = 'required|date';
        } else {
            $rules['tracking_number'] = 'required|string';
            $rules['sold_quantity']  ='required';
            $rules['single_sold_price'] ='required';
            $rules['discount_percent'] ='required';
            $rules['date_of_sale'] ='nullable|date';
        }

        return $rules;
    }

    private function isPaymentRequired()
    {
        $order = $this->order ?? null;
        return $order && $order->payment && $order->payment->payment_status === 2;
    }

    public function messages()
    {
        return [
            'purchase_id.required' => 'The product ID field is required.',
            'purchase_id.array' => 'The product ID must be an array.',
            'purchase_id.*.required' => 'Product ID field is required.',
            'purchase_id.*.numeric' => 'Product ID must be a numeric value.',
            'tracking_number.required' => 'Tracking number field is required.',
            'tracking_number.array' => 'Tracking number must be an array.',
            'tracking_number.*.required' => 'Tracking number field is required.',
            'tracking_number.*.string' => 'Tracking number must be a string value.',
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
