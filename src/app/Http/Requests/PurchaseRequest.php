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
        $rules = [];
        
        if($this->isPaymentRequired()) {
            $rules['image'] = "nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048";
            $rules['name'] = 'required|string';
            $rules['image_path'] = 'nullable|string';
            $rules['supplier_id'] = 'required|integer';
            $rules['category_id'] = 'required|integer';
            $rules['subcategories'] = 'nullable|array';
            $rules['notes'] = "nullable|string";
            $rules['brands'] = "nullable|array";
            $rules["code"] = 'required|string|max:20';
            $rules["expected_delivery_date"] = 'required|date';
            $rules["expected_date_of_payment"] = 'required|date';
            $rules["discount_percent"] = 'required|integer|min:0';
            $rules["price"] = 'required|numeric|min:1';
            $rules["quantity"] = 'required|integer|min:1';
            $rules["weight"] = 'nullable|integer|min:0';
            $rules["height"] = 'nullable|integer|min:0';
            $rules["color"] = 'nullable|string';
            $rules["invoice_number"] = 'required|string';
            $rules["invoice_date"] = 'required|date';    
        }

        return $rules;
    }
    private function isPaymentRequired()
    {
        $purchase = $this->purchase ?? null;
        return $purchase && $purchase->payment && $purchase->payment->payment_status === 2;
    }
}
