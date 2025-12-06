<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashbookResource extends JsonResource
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
            'title'         => $this->title,
            'description'   => strip_tags($this->description),
            'status'        => $this->status,
            'business'      => new BusinessResource($this->whenLoaded('business')),
            'members'       => MemberResource::collection($this->whenLoaded('members')),
            'transactions'  => TransactionResource::collection($this->whenLoaded('entries')),
            'creator'       => new UserResource($this->whenLoaded('creator')),
            'created_at'    => Carbon::parse($this->created_at)->format('d M, Y h:i A'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d M, Y h:i A'),
        ];
    }
}
