<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Models\User;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileService extends GenericService
{
    use FileTrait;

    public function __construct(private GroupUserService $groupUserService)
    {
        parent::__construct(new File());
    }

    private function uploadFileLogic($validatedData, $groupName)
    {
        $validatedData['file_url'] = $this->uploadFile($validatedData['file'], '/' . $groupName . '/');
        $validatedData['file_name'] = $validatedData['file']->getClientOriginalName();

        return $validatedData;
    }
    public function getFilesGroupId($fileIds)
    {
        $files = File::whereIn('id', $fileIds)
            ->whereIsAccepted(1)->pluck('group_id')->toArray();
        $groupIds = array_unique($files);
        return sizeof($groupIds) > 1 ? false : $groupIds;
    }

    public function store($validatedData)
    {
        DB::beginTransaction();

        $groupName = Group::whereId($validatedData['group_id'])->first()->group_name;
        $validatedData['publisher_id'] = Auth::user()->id;
        $validatedData = $this->uploadFileLogic($validatedData, $groupName);


        if ($this->groupUserService->checkIfAuthUserOwnTheGroup($validatedData['publisher_id'], $validatedData['group_id'])) {
            $validatedData['is_accepted'] = 1;
        }
        File::create($validatedData);

        DB::commit();
    }

    public function getFileRequests($validateData)
    {

        /**
         * @var User $user
         */
        $user = Auth::user();

        if (isset($validateData['group_id'])) {
            $ownedGroupIds = array_values([$validateData['group_id']]);
        } else {
            $ownedGroupIds = $user->groups()
                ->wherePivot('is_owner', 1)
                ->select('groups.id')
                ->pluck('id')
                ->toArray();
        }


        $fileRequests = File::whereIn('group_id', $ownedGroupIds)->whereIsAccepted(0)->get();

        return $fileRequests;
    }

    public function acceptOrRejectRequest($validatedData)
    {

        $model = File::find($validatedData['id']);
        if ($model->is_accepted != '0') {
            throw new Exception("Request not found !", 404);
        }

        switch ($validatedData['action']) {
            case 'accept':
                $model->is_accepted = '1';
                break;
            case 'reject':
                $model->is_accepted = '-1';
                break;
        }

        $model->save();

        return $validatedData['action'];
    }

    public function getFilesInGroup($validatedData)
    {
        return File::whereGroupId($validatedData['group_id'])->whereIsAccepted(1)->get();
    }

    public function checkIfFilesFree($fileIds)
    {
        $files =  File::whereIn('id', $fileIds)
            ->whereIsAccepted(1)
            ->whereStatus('FREE')
            ->get();

        return sizeof($files) == sizeof($fileIds);
    }

    public function isCheckInOwner($fileIds)
    {
        $files =  File::whereIn('id', $fileIds)
            ->whereIsAccepted(1)
            ->whereStatus('RESERVED')
            ->whereCurrentReserverId(Auth::id())
            ->get();

        return sizeof($files) == sizeof($fileIds);
    }
    public function checkIn($validatedData)
    {
        DB::beginTransaction();

        $files = File::lockForUpdate()->whereIn('id', $validatedData['file_ids'])->whereIsAccepted(1)->get();


        foreach ($files as $file) {
            $file->current_reserver_id = Auth::user()->id;
            $file->status = 'RESERVED';
            $file->save();
        }
        DB::commit();

        return count($files) > 1;
    }

    public function checkOut($validatedData)
    {
        $files = File::whereIn('id', $validatedData['file_ids'])->get();

        DB::beginTransaction();
        foreach ($files as $file) {
            $file->current_reserver_id = null;
            $file->status = 'FREE';
            $file->save();
        }
        DB::commit();
        return count($files) > 1;
    }

    public function delete($modelId)
    {
        $model = $this->findById($modelId);

        DB::beginTransaction();

        if ($model->status == 'FREE') {
            $model->delete();
        } else {
            throw new \Exception(__('messages.checkInFailed'), 403);
        }

        DB::commit();
    }

    public function downloadFile($modelId)
    {
        $file = $this->findById($modelId);

        if (!$file || $file->is_accepted != 1) {
            throw new \Exception(__('messages.FileNotFound'), 404);
        }

        $filePath = $file->file_url;

        return response()->download($filePath);
    }

    public function update($validatedData, $modelId)
    {
        DB::beginTransaction();

        $file = $this->findById($modelId);

        $groupName = Group::whereId($file->group_id)->first()->group_name;

        $validatedData = $this->uploadFileLogic($validatedData, $groupName);

        $file->update($validatedData);

        DB::commit();

        return $file;
    }

}
