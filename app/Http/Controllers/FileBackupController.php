<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileBackupRequest;
use App\Http\Resources\FileBackupResource;
use App\Services\FileBackupService;
use App\Models\FileBackup;

class FileBackupController extends GenericController
{
    private FileBackupService $fileBackupService;


    public function __construct(FileBackupService $fileBackupService)
    {
        $this->fileBackupService = $fileBackupService;


        $this->middleware(
            [
                'check.file.access:findById'
            ]
        )->only(['getFileVersions']);

        parent::__construct(new FileBackupRequest(), new FileBackupResource([]), new FileBackupService(new FileBackup()));
    }

    public function getFileVersions($fileId)
    {
        $fileVersions = $this->fileBackupService->getFileVersions($fileId);

        return $this->successResponse(
            $this->toResource($fileVersions, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }
}
