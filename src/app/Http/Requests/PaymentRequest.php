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
        return [
            "date_of_payment" => "required|date",
            "price" => "required|numeric|min:0",
            "quantity" => "required|integer|min:0"
        ];
    }

    public function __get($key)
    {
        return $this->validated()[$key] ?? null;
    }
}
