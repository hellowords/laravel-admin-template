<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray ($request)
    {
        return [
            'id'             => $this->id,
            'account'        => $this->account,
            'email'          => $this->email,
            'roles'          => $this->getRoleNames(),
            'permissions'    => $this->getAllPermissions()->pluck('name'),
            'last_active_at' => $this->profile->last_active_at,
        ];
    }
}
