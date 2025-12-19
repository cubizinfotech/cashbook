<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'id'            => $this->id,
            'name'          => $this->name,
            'date_of_birth' => Carbon::parse($this->date_of_birth)->format('d M, Y'),
            'gender'        => $this->gender,
            'phone'         => $this->phone,
            'profile_pic'   => $this->profile_pic,
            'description'   => $this->description,
            'address'       => $this->address,
            'zip_code'      => $this->zip_code,
            'status'        => $this->status,
            'country'       => new CountryResource($this->whenLoaded('country')),
            'state'         => new StateResource($this->whenLoaded('state')),
            'city'          => new CityResource($this->whenLoaded('city')),
            'user'          => new UserResource($this->whenLoaded('user')),
            'business'      => new BusinessResource($this->whenLoaded('business')),
            'business_role' => new BusinessRoleResource($this->whenLoaded('businessRole')),
            'cashbooks'     => CashbookResource::collection($this->whenLoaded('cashbooks')),
            'creator'       => new UserResource($this->whenLoaded('creator')),
            'created_at'    => Carbon::parse($this->created_at)->format('d M, Y h:i A'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d M, Y h:i A'),
        ];
    }
}

