<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileActionsLogRequest;
use App\Http\Resources\FileActionsLogResource;
use App\Services\FileActionsLogService;
use App\Models\FileActionsLog;

class FileActionsLogController extends GenericController
{
    private FileActionsLogService $fileActionsLogService;


    public function __construct(FileActionsLogService $fileActionsLogService)
    {
        $this->fileActionsLogService = $fileActionsLogService;
        
        parent::__construct(new FileActionsLogRequest(), new FileActionsLogResource([]), new FileActionsLogService(new FileActionsLog()));
    }
}
