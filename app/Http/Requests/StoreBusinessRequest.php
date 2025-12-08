<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'gst_number'  => 'nullable|string|max:255|unique:businesses,gst_number',
            'phone'       => 'nullable|string|max:255|unique:businesses,phone',
            'email'       => 'required|email|unique:businesses,email',
            'website'     => 'nullable|url|max:255|unique:businesses,website',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address'     => 'nullable|string|max:255',
            'country_id'  => 'nullable|exists:countries,id',
            'state_id'    => 'nullable|exists:states,id',
            'city_id'     => 'nullable|exists:cities,id',
            'zip_code'    => 'nullable|string|max:255',
        ];
    }
}

