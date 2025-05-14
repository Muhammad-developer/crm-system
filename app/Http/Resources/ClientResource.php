<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'company' => $this->company,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'notes'   => $this->notes,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
