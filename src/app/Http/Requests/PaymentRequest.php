<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {        
        $rules = [
            'id' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required',
            'date_of_payment' => 'required',
            'payment_method' => 'nullable',
            'partially_paid_price' => 'nullable|numeric',
            'payment_reference' => 'nullable',
            'payment_status' => 'nullable',
        ];
        return $rules;
    }

}
