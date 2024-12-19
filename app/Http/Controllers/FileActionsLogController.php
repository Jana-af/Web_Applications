<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileActionsLogRequest;
use App\Http\Resources\FileActionsLogResource;
use App\Services\FileActionsLogService;
use App\Models\FileActionsLog;
use App\Traits\FileTrait;

class FileActionsLogController extends GenericController
{
    use FileTrait;
    private FileActionsLogService $fileActionsLogService;


    public function __construct(FileActionsLogService $fileActionsLogService)
    {
        $this->fileActionsLogService = $fileActionsLogService;

        $this->middleware(
            [
                'check.file.access:findById'
            ]
        )->only(['getByFileId','getExcelReportByFileId']);



        $this->middleware(
            [
                'check.file.access:delete'
            ]
        )->only(['getByUserId','getExcelReportByUserId']);

        parent::__construct(new FileActionsLogRequest(), new FileActionsLogResource([]), new FileActionsLogService());
    }


    public function getByFileId($fileId){
        $fileLog = $this->fileActionsLogService->getByFileId($fileId);
        return $this->successResponse(
            $this->toResource($fileLog, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }

    public function getByUserId($userId){
        $fileLog = $this->fileActionsLogService->getByUserId($userId);

        return $this->successResponse(
            $this->toResource($fileLog, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }

    public function getExcelReportByFileId(FileActionsLogRequest $request, $fileId){
        $validatedData = $request->validated();
        $fileLog = $this->fileActionsLogService->getExcelReportByFileId($validatedData, $fileId);
        return $this->successResponse(
            $fileLog,
            __('messages.dataFetchedSuccessfully')
        );
    }

    public function getExcelReportByUserId(FileActionsLogRequest $request, $userId){
        $validatedData = $request->validated();
        $fileLog = $this->fileActionsLogService->getExcelReportByUserId($validatedData, $userId);

        return $this->successResponse(
            $fileLog,
            __('messages.dataFetchedSuccessfully')
        );
    }
}
