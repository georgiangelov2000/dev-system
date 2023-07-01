<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchasePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $isSingleRecord = !is_array($this->input('purchase_id'));
        
        $rules = [
            'purchase_id' => $isSingleRecord ? 'required' : 'required|array',
            'price' => $isSingleRecord ? 'required' : 'required|array',
            'quantity' => $isSingleRecord ? 'required' : 'required|array',
            'date_of_payment' => $isSingleRecord ? 'required' : 'required|array',
            'payment_method' => 'nullable',
            'payment_reference' => 'nullable',
            'payment_status' => 'nullable',
            'notes' => 'nullable',
        ];

        if ($this->isMethod('PUT')) {
            $rules['payment_method'] = 'required';
            $rules['payment_reference'] = 'required';
            $rules['payment_status'] = 'required';
        }

        if ($isSingleRecord) {
            $rules['purchase_id'] .= '|numeric';
            $rules['price'] .= '|numeric';
            $rules['quantity'] .= '|numeric';
            $rules['date_of_payment'] .= '|date';
        } else {
            $rules['purchase_id.*'] = 'required|numeric';
            $rules['price.*'] = 'required|numeric';
            $rules['quantity.*'] = 'required|numeric';
            $rules['date_of_payment.*'] = 'required|date';
        };

        return $rules;
    }

        /**
     * Get the validation messages for the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "purchase_id.required" => "The ID field is required.",
            "purchase_id.array" => "The ID field must be an array.",
            "purchase_id.*.required" => "The ID field is required.",
            "purchase_id.*.numeric" => "The ID field must be a numeric value.",
            "purchase_id.*.min" => "The ID field must be at least 0.",
            "date_of_payment.required" => "The date of payment field is required.",
            "date_of_payment.array" => "The date of payment field must be an array.",
            "date_of_payment.*.required" => "The date of payment field is required.",
            "date_of_payment.*.date" => "The date of payment field must be a valid date.",
            "price.required" => "The price field is required.",
            "price.array" => "The price field must be an array.",
            "price.*.required" => "The price field is required.",
            "price.*.numeric" => "The price field must be a numeric value.",
            "price.*.min" => "The price field must be at least 0.",
            "quantity.required" => "The quantity field is required.",
            "quantity.array" => "The quantity field must be an array.",
            "quantity.*.required" => "The quantity field is required.",
            "quantity.*.integer" => "The quantity field must be an integer value.",
            "quantity.*.min" => "The quantity field must be at least 0."
        ];
    }
}