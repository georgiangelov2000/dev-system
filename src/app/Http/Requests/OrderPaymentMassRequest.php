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
            "is_it_delivered" => "required|array",
            "is_it_delivered.*" => "required|string",
            "date_of_payment" => "required|array",
            "date_of_payment.*" => "required|date",
            "delivery_date" => "required|array",
            "delivery_date.*" => "required|date",
            "invoice_number" => "required|array",
            "invoice_number.*" => "required|string",
            "invoice_date" => "required|array",
            "invoice_date.*" => "required|date",
        ];
    }
}
