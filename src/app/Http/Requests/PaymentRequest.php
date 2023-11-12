<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $isDelivered = $this->input('is_it_delivered');
        $rules = [
            'id' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required',
            'date_of_payment' => $isDelivered ? 'required|date' : 'nullable',
            'payment_method' => 'required|integer',
            'partially_paid_price' => 'nullable|numeric',
            'payment_reference' => 'nullable',
            'is_it_delivered' => 'required',
            'delivery_date' =>  $isDelivered ? 'required|date' : 'nullable',
        ];
        return $rules;
    }

    /**
     * Get custom error messages for specific rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => 'The ID field is required.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'quantity.required' => 'The quantity field is required.',
            'partially_paid_price.numeric' => 'The partially paid price must be a number.',
            'is_it_delivered.required' => 'The Delivered field is required.',
        ];
    }
}
