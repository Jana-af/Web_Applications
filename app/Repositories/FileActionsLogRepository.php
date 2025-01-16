<?php

namespace App\Repositories;

use App\Models\FileActionsLog;
use Illuminate\Support\Collection;

class FileActionsLogRepository extends GenericRepository
{
    public function __construct(){
        parent::__construct(new FileActionsLog());
    }
    
    public function getByFileId(int $fileId): Collection
    {
        return FileActionsLog::whereFileId($fileId)->orderByDesc('created_at')->get();
    }

    public function getByUserId(int $userId): Collection
    {
        return FileActionsLog::whereUserId($userId)->orderByDesc('created_at')->get();
    }
}
