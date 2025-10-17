<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0|max:999999.99',
            'original_price' => 'nullable|numeric|min:0|max:999999.99',
            'quantity' => 'required|integer|min:0',
            'perishable' => 'required|in:yes,no',
            'expiration_date' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'name.unique' => 'A product with this name already exists.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'original_price.numeric' => 'Original cost must be a valid number.',
            'original_price.min' => 'Original cost cannot be negative.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
            'perishable.required' => 'Please specify if the product is perishable.',
            'perishable.in' => 'Perishable status must be either yes or no.',
            'expiration_date.date' => 'Expiration date must be a valid date.',
        ];
    }
}
