<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => ucwords($this->status),
            'cashbook'    => new CashbookResource($this->whenLoaded('cashbook')),
            'creator'     => new UserResource($this->whenLoaded('creator')),
            'created_at'  => Carbon::parse($this->created_at)->format('d M, Y h:i A'),
            'updated_at'  => Carbon::parse($this->updated_at)->format('d M, Y h:i A'),
        ];
    }
}
