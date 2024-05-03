<?php

namespace App\Http\Resources\Message;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'user_id' => $this->author->id,
            'user_name' => $this->author->name,
            'chat_id' => $this->chat_id,
            'body' => $this->body,
            'time' => $this->time,
            'is_owner' => $this->isOwner,
        ];
    }
}
