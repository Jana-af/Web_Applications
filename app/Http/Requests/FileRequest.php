<?php

namespace App\Http\Requests;

class FileRequest extends GenericRequest
{
    /**
     * Dynamically Get the the validation rules based on the request's action method.
     *
     * @return array
     */
    public function rules()
    {
        $method = request()->route()->getActionMethod();
        return $this->{$method . 'Validator'}();
    }

    private function storeValidator()
    {
        return [
            'file_name'         => 'required|string|max:255', //ToDo
            'group_id'         => 'required|Integer|exists:groups,id',
            'file'             => 'required|file',
        ];
    }

    private function acceptOrRejectRequestValidator(){
        return[
            'id' => 'required|exists:files,id',
            'action' => 'required|string|max:50|in:reject,accept'
        ];
    }

    private function getFilesInGroupValidator(){
        return[
            'id' => 'required|exists:groups,id',
        ];
    }

    private function checkInValidator(){
        return[
            'file_ids' => 'required|array',
            'file_ids*' => 'required|exists:files,id'
        ];
    }

    private function checkOutValidator(){
        return[
            'file_ids' => 'required|array',
            'file_ids*' => 'required|exists:files,id'
        ];
    }
}
