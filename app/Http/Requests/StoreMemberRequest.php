<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // make sure business_id is present first (it is in your rules)
        $businessId = $this->input('business_id');

        return [
            'business_id'     => 'required|exists:businesses,id',
            'business_role_id'=> 'required|exists:business_roles,id',
            'name'            => 'required|string|max:255',
            'date_of_birth'   => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            // Validate email unique *within the same business*
            'email'           => [
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                }),
                // keep user-level uniqueness if you need globally unique users:
                // Rule::unique('users', 'email'),
            ],
            // Validate phone unique *within the same business*
            'phone' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('members', 'phone')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                }),
            ],
            'password'        => 'nullable|string|min:8',
            'profile_pic'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'     => 'nullable|string',
            'address'         => 'nullable|string|max:255',
            'country_id'      => 'nullable|exists:countries,id',
            'state_id'        => 'nullable|exists:states,id',
            'city_id'         => 'nullable|exists:cities,id',
            'zip_code'        => 'nullable|string|max:255',
            'status'          => 'nullable|in:active,inactive,pending,suspended',
        ];
    }
}
