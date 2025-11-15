<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'cashbook_id' => $this->cashbook_id,
            'category_id' => $this->category_id,
            'payment_method_id' => $this->payment_method_id,
            'party_name' => $this->party_name,
            'remark' => $this->remark,
            'document' => $this->document,
            'amount' => (float) $this->amount,
            'transaction_datetime' => $this->transaction_datetime,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'cashbook' => new CashbookResource($this->whenLoaded('cashbook')),
            'category' => $this->whenLoaded('category'),
            'payment_method' => $this->whenLoaded('paymentMethod'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

