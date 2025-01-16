<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use App\Services\FileService;


class FileController extends GenericController
{
    private FileService $fileService;


    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;

        $this->middleware('check.file.access:findById')->only(['findById']);
        $this->middleware(['check.file.access:findById', 'check.file.status'])->only(['checkIn']);
        $this->middleware(['check.file.access:delete', 'check.file.status'])->only('delete');
        $this->middleware('check.file.access:delete')->only('acceptOrRejectRequest');
        $this->middleware('check.in.authority')->only(['checkOut', 'downloadFile']);
        $this->middleware(['check.in.authority', 'check.file.origin'])->only(['update']);

        $this->middleware([
            'check.user.in.group'
        ])->only(['store', 'getFilesInGroup']);

        $this->middleware(
            [
                'check.group.authority'
            ]
        )->only(['getFileRequests']);


        parent::__construct(new FileRequest(), new FileResource([]), new FileService());
    }

    public function store(){

        $validatedData = request()->validate($this->request->rules());
        $this->fileService->store($validatedData);

        return $this->successResponse(
            $this->toResource(null, ''),
            __('messages.dataAddedSuccessfully')
        );

    }

    public function update($modelId)
    {
        $validatedData = request()->validate($this->request->rules());

        $model = $this->fileService->update($validatedData, $modelId);

        return $this->successResponse(
            $this->toResource($model, $this->resource),
            __('messages.dataUpdatedSuccessfully')
        );
    }

    public function getFileRequests(FileRequest $fileRequest)
    {

        $validateData = $fileRequest->validated();

        $fileRequests = $this->fileService->getFileRequests($validateData);

        return $this->successResponse(
            $this->toResource($fileRequests, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }

    public function acceptOrRejectRequest(FileRequest $request)
    {
        $validatedData = $request->validated();
        $action = $this->fileService->acceptOrRejectRequest($validatedData);
        switch ($action) {
            case 'reject':
                $key = 'messages.requestRejectedSuccessfully';
                break;
            case 'accept':
                $key = 'messages.requestAcceptedSuccessfully';
                break;
        }
        return $this->successResponse(
            $this->toResource(null, ''),
            __($key)
        );
    }

    public function getFilesInGroup(FileRequest $request)
    {
        $validatedData = $request->validated();

        $items = $this->fileService->getFilesInGroup($validatedData);
        return $this->successResponse(
            $this->toResource($items, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }

    public function checkIn(FileRequest $request)
    {
        $validatedData = $request->validated();

        $bulk = $this->fileService->checkIn($validatedData);
        if ($bulk) {
            $key = 'messages.bulkCheckInSuccessfully';
        } else {
            $key = 'messages.checkInSuccessfully';
        }
        return $this->successResponse(
            $this->toResource(null, ''),
            __($key)
        );
    }

    public function checkOut(FileRequest $request)
    {
        $validatedData = $request->validated();

        $bulk = $this->fileService->checkOut($validatedData);
        if ($bulk) {
            $key = 'messages.bulkCheckOutSuccessfully';
        } else {
            $key = 'messages.checkOutSuccessfully';
        }
        return $this->successResponse(
            $this->toResource(null, ''),
            __($key)
        );
    }

    public function getUserCheckedInFiles(){
        $items = $this->fileService->getUserCheckedInFiles();
        return $this->successResponse(
            $this->toResource($items, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }
    public function downloadFile($modelId)
    {
        return $this->fileService->downloadFile($modelId);
    }
}
