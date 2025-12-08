<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cashbook_id'          => 'required|exists:cashbooks,id',
            'category_id'          => 'nullable|integer|exists:categories,id',
            'payment_method_id'    => 'nullable|integer|exists:payment_methods,id',
            'party_name'           => 'required|string|max:255',
            'remark'               => 'nullable|string',
            'document'             => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'amount'               => 'required|numeric|min:0',
            'transaction_datetime' => 'required|date',
            'description'          => 'nullable|string',
            'type'                 => 'required|in:in,out',
        ];
    }
}

