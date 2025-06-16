<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            "client_id" => $this->client_id,
            "professional_id" => $this->professional_id,
            'scheduled_at' => $this->scheduled_at,
            'status_type' => $this->status,
            'type' => $this->session_type,

            'client' => [
                "id" => $this->client->id ?? null,
                "name" => $this->client->name ?? null,
                "email" => $this->client->email ?? null,
                "role" => $this->client->role ?? null,
                "is_approved" => $this->client->is_approved ?? null,
            ],
            // other fields you want to include
        ];    }
}
