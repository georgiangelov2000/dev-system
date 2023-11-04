<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invoice_number' => "required|string",
            'invoice_date' => "required|date",
            'price' => 'required|numeric',
            'quantity' => "required|integer"
        ];
    }
}
