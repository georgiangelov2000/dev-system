<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            "image" => "image|mimes:jpeg,png,jpg,gif|max:2048",
            "name" => "required|string",
            "email" => "required|email",
            "phone" => "required|string",
            "address" => "required|string",
            "website" => "nullable|string",
            "zip" => "required|string",
            'country_id' => "required|integer",
            'state_id' => "required|integer",
            "notes" => "nullable|string",
            "categories" => "array",
            'image_path' => "nullable|string"
        ];
    }

}
