<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use App\Services\FileService;
use App\Models\File;

class FileController extends GenericController
{
    private FileService $fileService;


    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        
        parent::__construct(new FileRequest(), new FileResource([]), new FileService(new File()));
    }
}
