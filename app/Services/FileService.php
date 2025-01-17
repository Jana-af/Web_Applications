<?php

namespace App\Services;

use App\Annotations\Logger;
use App\Annotations\Transactional;
use App\Models\File;
use App\Models\FileBackup;
use App\Models\GroupUser;
use App\Models\User;
use App\Repositories\FileRepository;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class FileService extends GenericService
{
    use FileTrait;
    private GroupUserService $groupUserService;
    private GroupService $groupService;
    private FileBackupService $fileBackupService;
    private FileRepository $fileRepository;
    public function __construct()
    {
        $this->groupUserService = new GroupUserService(new GroupUser());
        $this->fileBackupService = new FileBackupService(new FileBackup());
        $this->fileRepository = new FileRepository();
        $this->groupService = new GroupService();
        parent::__construct(new File(), $this->fileRepository);
    }

    private function uploadFileLogic($validatedData, $groupName)
    {
        $validatedData['file_url'] = $this->uploadFile($validatedData['file'], '/' . $groupName . '/');
        $validatedData['file_name'] = $validatedData['file']->getClientOriginalName();

        return $validatedData;
    }


    public function getFilesGroupId($fileIds)
    {
        return $this->fileRepository->getFilesGroupId($fileIds);
    }

    #[Transactional]
    public function store($validatedData)
    {
        $groupName = $this->groupService->findById($validatedData['group_id'])->group_name;
        $validatedData['publisher_id'] = Auth::user()->id;
        $validatedData = $this->uploadFileLogic($validatedData, $groupName);


        if ($this->groupUserService->checkIfAuthUserOwnTheGroup($validatedData['group_id'], $validatedData['publisher_id'])) {
            $validatedData['is_accepted'] = 1;
        }
        $this->fileRepository->create($validatedData);
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

        return $this->fileRepository->getFileRequestsByGroupIds($ownedGroupIds);
    }

    #[Transactional]
    public function acceptOrRejectRequest($validatedData)
    {

        $model = $this->fileRepository->findById($validatedData['id']);
        if ($model->is_accepted != '0') {
            throw new Exception("Request not found !", 404);
        }

        switch ($validatedData['action']) {
            case 'accept':
                $this->fileRepository->updateFile($model, ['is_accepted' => '1']);
                break;
            case 'reject':
                $this->fileRepository->updateFile($model, ['is_accepted' => '-1']);
                break;
        }

        return $validatedData['action'];
    }

    public function getFilesInGroup($validatedData)
    {
        return $this->fileRepository->getFilesByGroupId($validatedData['group_id']);
    }

    public function checkIfFilesFree($fileIds)
    {
        $files =  $this->fileRepository->getFilesInStatus($fileIds, 'FREE', 1);
        return sizeof($files) == sizeof($fileIds);
    }


    public function isCheckInOwner($fileIds)
    {
        $files = $this->fileRepository->getReservedFilesByUser(Auth::id(), $fileIds);
        return sizeof($files) == sizeof($fileIds);
    }

    #[Logger]
    #[Transactional]
    public function checkIn($validatedData)
    {
        $files = $this->fileRepository->getByIds($validatedData['file_ids'], 1);

        foreach ($files as $file) {
            if ($file->status == 'RESERVED') {
                throw new Exception();
            }
            $this->fileRepository->updateFile(
                $file,
                [
                    'current_reserver_id' => Auth::id(),
                    'status' => 'RESERVED',
                    'check_in_time' => now()
                ]
            );
        }

        return count($files) > 0;
    }

    #[Logger]
    #[Transactional]
    public function checkOut($validatedData)
    {
        $files = $this->fileRepository->getByIds($validatedData['file_ids']);

        foreach ($files as $file) {
            $this->fileRepository->updateFile(
                $file,
                [
                    'current_reserver_id' => null,
                    'status' => 'FREE',
                    'check_in_time' => null
                ]
            );
        }

        return count($files) > 1;
    }

    #[Transactional]
    public function autoCheckOut()
    {
        $timeoutMinutes = 60;
        $timeoutLimit = now()->subMinutes($timeoutMinutes);

        $files = $this->fileRepository->getAutoCheckoutCandidates('RESERVED', $timeoutLimit);


        foreach ($files as $file) {
            $this->fileRepository->updateFile(
                $file,
                [
                    'current_reserver_id' => null,
                    'status' => 'FREE',
                    'check_in_time' => null
                ]
            );
        }

        return count($files);
    }

    public function getUserCheckedInFiles()
    {
        return $this->fileRepository->getUserCheckedInFiles();
    }

    #[Transactional]
    public function delete($modelId)
    {
        $file = $this->fileRepository->findById($modelId);

        if ($file->status == 'FREE') {
            $this->fileRepository->deleteFile($file);
        } else {
            throw new Exception(__('messages.checkInFailed'), 403);
        }
    }

    public function downloadFile($modelId)
    {
        $file = $this->fileRepository->findById($modelId);

        if (!$file || $file->is_accepted != 1) {
            throw new Exception(__('messages.FileNotFound'), 404);
        }

        $filePath = $file->file_url;

        return response()->download($filePath);
    }

    #[Logger]
    #[Transactional]
    public function update($validatedData, $modelId)
    {
        $file = $this->fileRepository->findById($modelId);
        if ($file->file_name != $validatedData['file']->getClientOriginalName()) {
            throw new Exception(__('messages.fileModificationFailed'), 409);
        }
        $fileBackUpData = [
            'file_id'       => $modelId,
            'file_url'        => $file->file_url,
            'version'       => $this->fileBackupService->getLatestVersionNumber($modelId) + 1,
            'modifier_id' => Auth::id(),
            'version_date' => $file->updated_at != null ? $file->updated_at : $file->created_at,
        ];

        $group = $this->groupService->findById($file->group_id);
        $validatedData = $this->uploadFileLogic($validatedData, $group->group_name);

        $this->fileRepository->updateFile($file, $validatedData);
        $this->fileBackupService->store($fileBackUpData);

        return $file;
    }

    public function getDiff($validatedData)
    {
        $fromFileUrl = '';
        $toFileUrl = '';


        if (isset($validatedData['file_id'])) {
            $file = $this->findById($validatedData['file_id']);
            $toFileUrl = $file->file_url;

            $fileVersion = $this->fileBackupService->findById($validatedData['first_file_version_id']);

            $fromFileUrl = $fileVersion->file_url;
        }

        if (isset($validatedData['second_file_version_id'])) {
            $fileVersion1 = $this->fileBackupService->findById($validatedData['first_file_version_id']);
            $fileVersion2 = $this->fileBackupService->findById($validatedData['second_file_version_id']);

            if ($fileVersion1->version > $fileVersion2->version) {
                $toFileUrl = $fileVersion1->file_url;
                $fromFileUrl = $fileVersion2->file_url;
            } else {
                $toFileUrl = $fileVersion2->file_url;
                $fromFileUrl = $fileVersion1->file_url;
            }
        }


        if (!str_starts_with(mime_content_type($toFileUrl), 'text/') || !str_starts_with(mime_content_type($fromFileUrl), 'text/')) {
            throw new Exception(__('messages.invalidMimeType'), 400);
        }


        $from = file_get_contents($fromFileUrl);
        $to = file_get_contents($toFileUrl);

        $outputBuilder = new UnifiedDiffOutputBuilder(
            "--- Original\n+++ New",
        );

        $differ = new Differ($outputBuilder);
        return $differ->diff($from, $to);
    }
}
