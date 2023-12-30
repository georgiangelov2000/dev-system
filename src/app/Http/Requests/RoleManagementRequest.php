<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleManagementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'users' => 'required|array',
            'permissions' => 'required|array',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The role name is required.',
            'name.string' => 'The role name must be a string.',
            'users.required' => 'At least one user must be assigned to the role.',
            'users.array' => 'Invalid users data format.',
            'permissions.required' => 'At least one permission must be assigned to the role.',
            'permissions.array' => 'Invalid permissions data format.',
        ];
    }
}
