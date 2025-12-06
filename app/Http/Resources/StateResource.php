<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'state_code' => $this->state_code,
            'country_id' => $this->country_id,
            'country'    => new CountryResource($this->whenLoaded('country')),
            'status'     => $this->status,
            'creator'    => new UserResource($this->whenLoaded('creator')),
            'created_at' => Carbon::parse($this->created_at)->format('d M, Y h:i A'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d M, Y h:i A'),
        ];
    }
}
