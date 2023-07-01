<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            "name" => "required|string",
            "email" => "required|email",
            "phone" => "required|string",
            "address" => "required|string",
            "website" => "nullable|string",
            "zip" => "required|string",
            'country_id' => "required|integer",
            'state_id' => "required|integer",
            "notes" => "nullable|string"
        ];
    }
}
