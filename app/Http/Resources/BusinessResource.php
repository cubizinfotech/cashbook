<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => strip_tags($this->description),
            'gst_number'    => $this->gst_number,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'website'       => $this->website,
            'logo'          => $this->logo,
            'address'       => $this->address,
            'zip_code'      => $this->zip_code,
            'status'        => $this->status,
            'country'       => new CountryResource($this->whenLoaded('country')),
            'state'         => new StateResource($this->whenLoaded('state')),
            'city'          => new CityResource($this->whenLoaded('city')),
            'members'       => MemberResource::collection($this->whenLoaded('members')),
            'cashbooks'     => CashbookResource::collection($this->whenLoaded('cashbooks')),
            'total_members' => $this->members()->count(),
            'total_cashbooks'=> $this->cashbooks()->count(),
            'creator'       => new UserResource($this->whenLoaded('creator')),
            'created_at'    => Carbon::parse($this->created_at)->format('d M, Y h:i A'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d M, Y h:i A'),
            'business_role' => ucwords(auth()->id() == $this->created_by
                                ? 'creator'
                                : optional(collect($this->members ?? [])->firstWhere('user_id', auth()->id()))->businessRole->name),
        ];
    }
}

