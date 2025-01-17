<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileBackupRequest;
use App\Http\Resources\FileActionsLogResource;
use App\Http\Resources\FileBackupResource;
use App\Models\FileActionsLog;
use App\Services\FileBackupService;
use App\Models\FileBackup;
use App\Traits\FileTrait;

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
        )->only(['getFileVersions', 'downloadFile']);

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

    public function downloadFile($modelId)
    {
        return $this->fileBackupService->downloadFile($modelId);
    }
}
