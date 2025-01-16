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
            'group_id'         => 'required|Integer|exists:groups,id',
            'file'             => 'required|file',
        ];
    }

    private function acceptOrRejectRequestValidator()
    {
        return [
            'id' => 'required|exists:files,id',
            'action' => 'required|string|max:50|in:reject,accept'
        ];
    }

    private function getFilesInGroupValidator()
    {
        return [
            'group_id' => 'required|exists:groups,id',
        ];
    }

    private function checkInValidator()
    {
        return [
            'file_ids' => 'required|array',
            'file_ids*' => 'required|exists:files,id'
        ];
    }

    private function checkOutValidator()
    {
        return [
            'file_ids' => 'required|array',
            'file_ids*' => 'required|exists:files,id'
        ];
    }

    private function getFileRequestsValidator()
    {
        return [
            'group_id'  => 'nullable|integer|exists:groups,id'
        ];
    }

    private function updateValidator()
    {
        return [
            'file'             => 'required|file',
        ];
    }
    private function getDiffValidator()
    {
        return [
            'file_id'                => 'required_without:second_file_version_id|nullable|exists:files,id',
            'first_file_version_id'  => 'required|exists:file_backups,id',
            'second_file_version_id' => 'required_without:file_id|nullable|exists:file_backups,id',
        ];
    }
}
