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
        $method = request()->route()->getActionMethod();
        return $this->{$method . 'Resource'}();
    }

    public function getByFileIdResource(){
        return [
            'id'             =>  $this->id,
            'user'           =>  $this->user ? new UserResource($this->user) : null,
            'action'         =>  $this->action,
            'created_at'     =>  $this->created_at,
        ];
    }

    public function getByUserIdResource(){
        return [
            'id'             =>  $this->id,
            'file'           =>  $this->file ? new FileResource($this->file) : null,
            'action'         =>  $this->action,
            'created_at'     =>  $this->created_at,
        ];
    }
}
