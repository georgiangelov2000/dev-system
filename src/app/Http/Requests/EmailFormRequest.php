<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "email" => "required|string",
            "client_email" => "required|string",
            "message_type" => "required|string",
            "title" => "required|string",
            "content" => "required|string",
        ];
    }
}
