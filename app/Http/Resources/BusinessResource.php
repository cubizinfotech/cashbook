<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
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
            'name' => $this->name,

            'description' => strip_tags($this->description),
            'gst_number' => $this->gst_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'logo' => $this->logo,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'zip_code' => $this->zip_code,
            'status' => $this->status,
            'country' => $this->whenLoaded('country'),
            'state' => $this->whenLoaded('state'),
            'city' => $this->whenLoaded('city'),
            'members' => MemberResource::collection($this->whenLoaded('members')),
            'users' => UserResource::collection($this->whenLoaded('users')),

            'cashbooks' => CashbookResource::collection($this->whenLoaded('cashbooks')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_current_user' => auth()->user()->email === $this->email

        ];
    }
}

