<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => "required|integer",
            'email'=> 'nullable|email',
            'notification_email'=> 'nullable|email',
            'name' => 'nullable|string',
            'country' => 'nullable|integer',
            'state' => 'nullable|integer',
            'phone_number' =>  'nullable|string',
            'tax_number' => 'nullable|string',
            'address' => 'nullable|string',
            'website' => 'nullable|string',
            'owner_name' => 'nullable|string',
            'bussines_type' => 'nullable|string',
            'registration_date' => 'nullable|date',
            'image' => 'nullable|file',
        ];
    }
}
