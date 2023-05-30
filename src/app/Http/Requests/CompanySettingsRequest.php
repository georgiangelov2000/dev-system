<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanySettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "image" => "image|mimes:jpeg,png,jpg,gif|max:2048",
            "email" => "required|string",
            'name' => "required|string",
            "country_id" => "required|integer",
            "state_id" => "required|integer",
            "phone_number" => "required|string",
            "tax_number" => "required|string",
            "address" => "required|string",
            "website" => "required|string",
            "owner_name" => "required|string",
            "bussines_type" => "required|string",
        ];
    }
}
