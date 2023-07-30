<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "tracking_number" => "required|string",
            "customer_id" => "required|integer",
            "package_name" => "required|string",
            "package_type" => "required|string",
            "delivery_method" => "required|string",
            "package_notes" => "nullable|string",
            "customer_notes" => "nullable|string",
            "order_id" => "required|array",
            'expected_delivery_date' => "required|date"
        ];
    }
}
