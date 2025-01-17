<?php

namespace App\Http\Resources;

class UserResource extends GenericResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
			'id'          =>  $this->id,
			'name'        =>  $this->name,
			'username'    =>  $this->username,
			'device_token'    =>  $this->device_token,
			'role'        =>  $this->role
        ];
    }
}
