<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'sub_categories' => "array",
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

}
