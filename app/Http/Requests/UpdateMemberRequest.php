<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
        $memberId = $this->route('member');

        return [
            'business_id' => 'required|exists:businesses,id',
            'business_role_id' => 'required|exists:business_roles,id',
            'password' => 'nullable|string|min:8',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($memberId)],
            'phone' => ['nullable', 'string', 'max:255', Rule::unique('members', 'phone')->ignore($memberId)],
            'profile_pic' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zip_code' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,pending,suspended',
        ];
    }
}

