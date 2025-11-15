<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'business_role_id' => $this->business_role_id,
            'name' => $this->name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_pic' => $this->profile_pic,
            'description' => $this->description,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'zip_code' => $this->zip_code,
            'status' => $this->status,
            'business' => new BusinessResource($this->whenLoaded('business')),
            'role' => $this->whenLoaded('role'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

