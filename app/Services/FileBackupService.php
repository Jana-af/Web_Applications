<?php

namespace App\Services;

use App\Models\FileBackup;
use App\Repositories\FileBackupRepository;

class FileBackupService extends GenericService
{
    private $fileBackupRepository;

    public function __construct()
    {
        $this->fileBackupRepository = new FileBackupRepository();
        parent::__construct(new FileBackup(), $this->fileBackupRepository );
    }
    public function getLatestVersionNumber($fileId)
    {
        return $this->fileBackupRepository->getLatestVersionNumber($fileId);
    }
    public function getFileVersions($fileId)
    {
        return $this->fileBackupRepository->getFileVersions($fileId);
    }

    public function downloadFile($modelId)
    {
        $file = $this->fileBackupRepository->findById($modelId);

        $filePath = $file->file_url;

        return response()->download($filePath);
    }
}
