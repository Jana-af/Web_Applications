<?php

namespace App\Services;

use App\Models\FileBackup;

class FileBackupService extends GenericService
{
    public function __construct(){
        parent::__construct(new FileBackup());
    }
    public function getLatestVersionNumber($fileId)
    {
        $latestVersion = FileBackup::whereFileId($fileId)->max('version');
        return  $latestVersion ?? 0;
    }
    public function getFileVersions($fileId)
    {
        return FileBackup::whereFileId($fileId)->get();
    }
}
