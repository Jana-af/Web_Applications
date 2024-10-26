<?php

namespace App\Http\Resources;

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
			'file_url'             =>  $this->file_url,
			'status'               =>  $this->status,
			'current_reserver_id'  =>  $this->current_reserver_id,
			'publisher_id'         =>  $this->publisher_id,
			'group_id'             =>  $this->group_id,
			'is_added'             =>  $this->is_added,
			'created_at'           =>  $this->created_at,
        ];
    }
}
