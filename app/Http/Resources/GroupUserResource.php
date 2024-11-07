<?php

namespace App\Http\Resources;

class GroupUserResource extends GenericResource
{
    public function toArray($request)
    {
        return [
			'id'          =>  $this->id,
			'group_name'  =>  $this->group['group_name'],
			'group_type'  =>  $this->group['group_type'],
            'user_name'    =>  $this->user['name']
        ];
    }
}
