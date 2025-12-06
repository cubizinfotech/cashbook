<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'id'                   => $this->id,
            'party_name'           => $this->party_name,
            'remark'               => $this->remark,
            'document'             => $this->document,
            'document_url'         => $this->document ? asset('storage/'.$this->document) : null,
            'amount'               => (float) $this->amount,
            'transaction_datetime' => Carbon::parse($this->transaction_datetime)->format('d M, Y h:i A'),
            'description'          => strip_tags($this->description),
            'type'                 => $this->type,
            'status'               => $this->status,
            'cashbook'             => new CashbookResource($this->whenLoaded('cashbook')),
            'category'             => new CategoryResource($this->whenLoaded('category')),
            'payment_method'       => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'creator'              => new UserResource($this->whenLoaded('creator')),
            'created_at'           => Carbon::parse($this->created_at)->format('d M, Y h:i A'),
            'updated_at'           => Carbon::parse($this->updated_at)->format('d M, Y h:i A'),
        ];
    }
}
