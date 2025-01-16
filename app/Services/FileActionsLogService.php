<?php

namespace App\Services;

use App\Models\FileActionsLog;
use App\Repositories\FileActionsLogRepository;
use App\Traits\FileTrait;

class FileActionsLogService extends GenericService
{
    use FileTrait;
    protected FileActionsLogRepository $fileActionsLogRepository;
    private UserService $userService;
    private FileService $fileService;

    public function __construct(){
        $this->fileActionsLogRepository = new FileActionsLogRepository();
        parent::__construct(new FileActionsLog(),  $this->fileActionsLogRepository);
    }

    public function getByFileId($fileId){
        return $this->fileActionsLogRepository->getByFileId($fileId);
    }

    public function getExcelReportByFileId($validatedData, $fileId){

        $list = $this->fileActionsLogRepository->getByFileId($fileId);
        $file = $this->fileService->findById($fileId);
        $collection = $list->map(function ($item) {
            $user = $this->userService->findById($item->user_id);

            return [
                'Username' => $user ? $user->name : 'Unknown',
                'Action' => $item->action,
                'Action Date' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $fileName = $file?->file_name ?? 'Unknown';
        $filePath = $this->generateExcelFile(
            $collection,
            '/Reports/',
            fileName: $fileName.'.xlsx'
        );

        if(isset($validatedData['pdf']) && $validatedData['pdf'] == true){
            return  url($this->convertExcelToPdf($filePath));
        }
        return  url($filePath);
   }
    public function getByUserId($userId){
        return $this->fileActionsLogRepository->getByUserId($userId);
    }

    public function getExcelReportByUserId($validatedData, $userId){

        $list = $this->fileActionsLogRepository->getByUserId($userId);

        $collection = $list->map(function ($item) {
            $file = $this->fileService->findById($item->file_id);

            return [
                'File Name' => $file ? $file->file_name : 'Unknown',
                'Action' => $item->action,
                'Action Date' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $user = $this->userService->findById($userId);
        $userName = $user?->name ?? 'Unknown';
        $filePath = $this->generateExcelFile(
            $collection,
            '/Reports/',
            fileName: $userName.'.xlsx'
        );

        if(isset($validatedData['pdf']) && $validatedData['pdf'] == true){
            return  url($this->convertExcelToPdf($filePath));
        }
        return  url($filePath);
   }
}
