<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPaymentMassRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "order_id" => "required|array",
            "order_id.*" => "required|string",
            "payment_method" => "required|array",
            "payment_method.*" => "required|string",
            "date_of_payment" => "required|array",
            "date_of_payment.*" => "required|date",
            "delivery_date" => "required|array",
            "delivery_date.*" => "required|date",
            "invoice_number" => "required|array",
            "invoice_number.*" => "required|string",
            "payment_reference" => "required|array",
            "payment_reference.*" => "nullable|string",
            "invoice_date" => "required|array",
            "invoice_date.*" => "required|date",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_id.required' => 'The order ID is required.',
            'order_id.*.required' => 'Each order ID must be a string.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.*.required' => 'Each payment method must be a string.',
            'date_of_payment.required' => 'The date of payment is required.',
            'date_of_payment.*.required' => 'Each date of payment must be a valid date.',
            'delivery_date.required' => 'The delivery date is required.',
            'delivery_date.*.required' => 'Each delivery date must be a valid date.',
            'invoice_number.required' => 'The invoice number is required.',
            'invoice_number.*.required' => 'Each invoice number must be a string.',
            'payment_reference.required' => 'The payment reference is required.',
            'invoice_date.required' => 'The invoice date is required.',
            'invoice_date.*.required' => 'Each invoice date must be a valid date.',
        ];
    }
}
