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
        
        parent::__construct(new FileBackupRequest(), new FileBackupResource([]), new FileBackupService(new FileBackup()));
    }
}
