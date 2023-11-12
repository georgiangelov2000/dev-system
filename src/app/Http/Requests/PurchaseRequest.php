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
            "supplier_id" => "required|integer|not_in:0",
            "category_id" => "required|integer|not_in:0",
            "subcategories" => "nullable|array",
            "notes" => "nullable|string",
            "brands" => "nullable|array",
            "image_path" => "nullable",          
        ];

        if($this->isPaymentRequired()) {
            $rules["code"] = 'required|string|max:20';
            $rules["expected_delivery_date"] = 'required|date';
            $rules["expected_date_of_payment"] = 'required|date';
            $rules["discount_percent"] = 'required|integer|min:0';
            $rules["price"] = 'required|numeric|min:1';
            $rules["quantity"] = 'required|integer|min:1';            
        }

        return $rules;
    }
    private function isPaymentRequired()
    {
        $purchase = $this->purchase ?? null;
        return $purchase && $purchase->payment && $purchase->payment->payment_status === 2;
    }
}
