<?php

namespace App\Http\Resources;

class GroupResource extends GenericResource
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
			'group_name'  =>  $this->group_name,
			'group_type'  =>  $this->group_type,
            'is_owner'    =>  $this['pivot']['is_owner']
        ];
    }
}
