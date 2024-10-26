<?php

namespace App\Http\Resources;

class GroupUserResource extends GenericResource
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
			'id'           =>  $this->id,
			'user_id'      =>  $this->user_id,
			'group_id'     =>  $this->group_id,
			'is_owner'     =>  $this->is_owner,
			'is_accepted'  =>  $this->is_accepted,
			'created_at'   =>  $this->created_at,
        ];
    }
}
