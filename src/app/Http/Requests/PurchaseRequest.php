<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "image" => "nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048",
            "name" => "required|string",
            "code" => !$this->isPaymentRequired() ? 'required|string' : 'nullable|string',
            "supplier_id" => "required|integer|not_in:0",
            "category_id" => "required|integer|not_in:0",
            "subcategories" => "nullable|array",
            "notes" => "nullable|string",
            "brands" => "nullable|array",
            "image_path" => "nullable",
            "expected_delivery_date" => !$this->isPaymentRequired() ? 'required|date' : 'nullable|date',
            'expected_date_of_payment' => !$this->isPaymentRequired() ? 'required|date' : 'nullable|date',
            'discount_percent' => !$this->isPaymentRequired() ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'price' => !$this->isPaymentRequired() ? 'required|numeric|min:1' : 'nullable|numeric|min:0',
            'quantity' => !$this->isPaymentRequired() ? 'required|integer|min:1' : 'nullable|integer|min:0',
        ];

        return $rules;
    }
    private function isPaymentRequired()
    {
        $purchase = $this->purchase ?? null;

        return $purchase && $purchase->payment && $purchase->payment->payment_status === 2;
    }
}
