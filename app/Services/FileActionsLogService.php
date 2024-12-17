<?php

namespace App\Services;

use App\Models\File;
use App\Models\FileActionsLog;
use App\Models\User;
use App\Traits\FileTrait;

class FileActionsLogService extends GenericService
{

    use FileTrait;
    public function __construct(){
        parent::__construct(new FileActionsLog());
    }

    public function getByFileId($fileId){
        return FileActionsLog::whereFileId($fileId)->orderByDesc('created_at')->get();
    }

    public function getExcelReportByFileId($fileId){

        $list = FileActionsLog::whereFileId($fileId)->orderByDesc('created_at')->get();

        $collection = $list->map(function ($item) {
            $file = File::find($item->file_id);
            $user = User::find($item->user_id);

            return [
                'Username' => $user ? $user->name : 'Unknown',
                'Action' => $item->action,
                'Action Date' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $fileName = File::find($fileId)?->file_name ?? 'Unknown';
        $filePath = $this->generateExcelFile(
            $collection,
            '/Reports/',
            fileName: $fileName.'.xlsx'
        );

        // return  url($this->convertExcelToPdf($filePath));
        return  url($filePath);
   }
    public function getByUserId($userId){
        return FileActionsLog::whereUserId($userId)->orderByDesc('created_at')->get();
    }

    public function getExcelReportByUserId($userId){

        $list =  FileActionsLog::whereUserId($userId)->orderByDesc('created_at')->get();

        $collection = $list->map(function ($item) {
            $file = File::find($item->file_id);

            return [
                'File Name' => $file ? $file->file_name : 'Unknown',
                'Action' => $item->action,
                'Action Date' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $userName = User::find($userId)?->name ?? 'Unknown';
        $filePath = $this->generateExcelFile(
            $collection,
            '/Reports/',
            fileName: $userName.'.xlsx'
        );

        return  url($filePath);
   }
}
