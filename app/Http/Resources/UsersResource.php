<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                "username"=> $this->username,
                "firstname" => $this->firstname,
                "lastname" => $this->lastname,
                "name"=> $this->firstname . ' ' . $this->lastname,
                "email"=> $this->email,
                'created_at'=> $this->created_at,
                'updated_at'=> $this->updated_at
        ];
    }
}
