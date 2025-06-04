<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Alert extends JsonResource
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
            'threshold' => $this->threshold,
            'response' => $this->response,
            'details' => $this->details,
            'status' => $this->status,
            'user' => $this->user,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'assigned_users' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                ];
            }),
             'assigned_user_names' => $this->users->pluck('username')->implode(', '),
        ];
    }
}
