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
            "delievery_method" => "required|string",
            "delievery_date" => "required|date",
            "package_notes" => "string",
            "customer_notes" => "string",
            "order_id" => "required|array",
            "total_order_price" => "required|array"
        ];
    }
}
