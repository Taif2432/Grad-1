<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'session_id' => $this->session_id,
        'client' => new UserResource($this->session->client),
        'professional' => new UserResource($this->session->professional),
        'notes' => $this->notes,
        'started_at' => $this->started_at,
        'ended_at' => $this->ended_at,
        'created_at' => $this->created_at,
    ];
}

}
