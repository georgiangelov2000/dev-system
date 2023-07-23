<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_payment_id' => "required|integer",
            'invoice_number' => "required|string",
            'invoice_date' => "required|date",
            'price' => 'required|numeric',
            'quantity' => "required|integer"
        ];
    }
}
