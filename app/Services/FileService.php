<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileService extends GenericService
{
    use FileTrait;

    private GroupUserService $groupUserService; //ToDo
    public function __construct(GroupUserService $groupUserService)
    {
        $this->groupUserService = $groupUserService;
        parent::__construct(new File());
    }

    public function store($validatedData)
    {
        DB::beginTransaction();

        $groupName = Group::whereId($validatedData['group_id'])->first()->group_name;
        $validatedData['publisher_id'] = Auth::user()->id;
        $validatedData['file_url'] = $this->uploadFile($validatedData['file'], '/'. $groupName .'/' );

        if($this->groupUserService->checkIfAuthUserOwnTheGroup($validatedData['publisher_id'] , $validatedData['group_id'])){
            $validatedData['is_accepted'] = 1;
        }
        $model = File::create($validatedData);

        DB::commit();

        //ToDo
    }

    public function acceptOrRejectRequest($validatedData){

        $model = File::find($validatedData['id']);
        switch($validatedData['action']){
            case 'accept' :
                $model->is_accepted = '1';
            break;
            case 'reject' :
                $model->is_accepted = '-1';
            break;
        }

        $model->save();

        return $validatedData['action'];
    }

    public function getFilesInGroup($validatedData){
        $items = File::whereGroupId($validatedData['id'])->whereIsAccepted(1)->get();
        return $items;
    }

    public function checkIn($validatedData){                                      //ToDo
        $files = File::whereIn('id',$validatedData['file_ids'])->whereIsAccepted(1)->get();

        if (count($files) > 1) {
            $bulk = 1;
            $key = 'messages.bulkCheckInFailed';
        }else{
            $key = 'messages.checkInFailed';
        }

        DB::beginTransaction();
        foreach($files as $file){

            if ($file->status == 'RESERVED') {
                throw new \Exception(
                    __($key),
                    403
                ); //ToDo
            }

            $file->current_reserver_id = Auth::user()->id;
            $file->status = 'RESERVED';
            $file->save();
        }
        DB::commit();
        return $bulk;
    }

    public function checkOut($validatedData){
        $files = File::whereIn('id',$validatedData['file_ids'])->get();

        if (count($files) > 1) {
            $bulk = 1;
        }

        DB::beginTransaction();
        foreach($files as $file){
            $file->current_reserver_id = null;
            $file->status = 'FREE';
            $file->save();
        }
        DB::commit();
        return $bulk;
    }

    public function delete($modelId)
    {
        $model = $this->findById($modelId);

        DB::beginTransaction();

        if($model->status == 'FREE'){
            $model->delete();
        }else{
            throw new \Exception(__('messages.checkInFailed'),403);
        }

        DB::commit();
    }

    public function downloadFile($modelId)
    {
        $file = $this->findById($modelId);

        if (!$file || $file->is_accepted != 1) {
            throw new \Exception(__('messages.FileNotFound'),404);
        }

        $filePath = $file->file_url;

        return response()->download($filePath);

        // return Storage::download($filePath, $file->file_name);
    }
}
