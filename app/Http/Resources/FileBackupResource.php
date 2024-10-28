<?php

namespace App\Http\Resources;

class FileBackupResource extends GenericResource
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
			'file_id'     =>  $this->file_id,
			'file_url'    =>  $this->file_url,
			'created_at'  =>  $this->created_at,
        ];
    }
}