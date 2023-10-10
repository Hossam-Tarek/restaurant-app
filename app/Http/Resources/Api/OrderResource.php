<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\OrderProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'total' => $this->total,
            'products' => OrderProductResource::collection($this->products),
            'created_at' => $this->created_at
        ];
    }
}
