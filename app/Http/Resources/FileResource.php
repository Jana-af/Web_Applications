<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\URL;

class FileResource extends GenericResource
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
			'id'                   =>  $this->id,
			'file_name'            =>  $this->file_name,
			'file_url'             =>  URL::asset('') . $this->file_url,
			'status'               =>  $this->status,
			'current_reserver_id'  =>  $this->current_reserver_id, //ToDo
			'publisher'            =>  $this->user->name,
			'group_id'             =>  $this->group_id,
			'is_accepted'          =>  $this->is_accepted,
			'created_at'           =>  $this->created_at,
        ];
    }
}
