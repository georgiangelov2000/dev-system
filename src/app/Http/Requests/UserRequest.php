<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $user = $this->route('user'); // Get the user ID from the route
        return [
            "image" => "nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048",
            'email' => [
                'required',
                'string',
                Rule::unique('users')->ignore($user),
                function ($attribute, $value, $fail) use ($user) {
                    // Check for uniqueness only if the email is changed
                    if ($user && $user->email === $value) {
                        return;
                    }
                    if (User::where('email', $value)->exists()) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'username' => [
              'required',
              'string',
              'min:10',
              Rule::unique('users')->ignore($user),
                function ($attribute, $value, $fail) use ($user) {
                    // Check for uniqueness only if the email is changed
                    if ($user && $user->username === $value) {
                        return;
                    }
                    if (User::where('username', $value)->exists()) {
                        $fail('The username has already been taken.');
                    }
                },
            ],
            'password' => 'required|string|min:10|required_with:confirm-password|same:confirm-password',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'gender' => 'nullable|string',
            'role_id' => 'required|integer',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'card_id' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'pdf' => 'nullable|file|mimes:pdf|max:2048'
        ];
    }

}
