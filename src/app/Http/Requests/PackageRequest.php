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
        $commonRules = [
            "package_name" => "required|string",
            "package_notes" => "nullable|string",
            "customer_notes" => "nullable|string",
        ];

        $editableRules = [
            "customer_id" => "required|integer",
            "tracking_number" => "required|string",
            "package_type" => "required|string",
            "delivery_method" => "required|string",
            "order_id" => "required|array",
            "expected_delivery_date" => "required|date",
        ];


        return $this->isEditableRequired() ? $commonRules : array_merge($commonRules, $editableRules);
    }

    private function isEditableRequired()
    {
        return $this->package->is_it_delivered;
    }
}
