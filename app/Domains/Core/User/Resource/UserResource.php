<?php

namespace App\Domains\Core\User\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $roleId = optional($this->roles->first())->id;
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'country_code'  => $this->country_code,
            'phone'         => $this->phone,
            'status'        => $this->status,
            'user_type'     => array_search($roleId, config('constant.roles')),
            'profile_image' => $this->profile_image_url
        ];
    }
}
