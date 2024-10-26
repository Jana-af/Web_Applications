<?php

namespace App\Http\Resources;

class FileActionsLogResource extends GenericResource
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
			'id'             =>  $this->id,
			'file_id'        =>  $this->file_id,
			'user_id'        =>  $this->user_id,
			'action'         =>  $this->action,
			'to_group'       =>  $this->to_group,
			'old_file_name'  =>  $this->old_file_name,
			'new_file_name'  =>  $this->new_file_name,
			'created_at'     =>  $this->created_at,
		];
    }
}
