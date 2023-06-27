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
            "id" => "required|array",
            'id.*' => "required|numeric|min:0",
            "date_of_payment" => "required|array",
            "date_of_payment.*" => "required|date",
            "price" => "required|array",
            "price.*" => "required|numeric|min:0",
            "quantity" => "required|array",
            "quantity.*" => "required|integer|min:0"
        ];
    }

    public function __get($key)
    {
        return $this->validated()[$key] ?? null;
    }
}
