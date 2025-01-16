<?php

namespace App\Repositories;

use App\Models\FileBackup;
use Illuminate\Support\Collection;

class FileBackupRepository extends GenericRepository
{
    public function __construct(){
        parent::__construct(new FileBackup());
    }
    public function getLatestVersionNumber(int $fileId): int
    {
        return FileBackup::whereFileId($fileId)->max('version') ?? 0;
    }

    public function getFileVersions(int $fileId): Collection
    {
        return FileBackup::whereFileId($fileId)->get();
    }
}
