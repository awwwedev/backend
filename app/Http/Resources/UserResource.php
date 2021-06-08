<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'role' => $this->role->role,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'hasNewMessage' => $this->when($request->has('withHasNewMessage'),
                $this->ticket()->whereStatus(Ticket::STATE_NEW)->count()
            )
        ];
    }
}
