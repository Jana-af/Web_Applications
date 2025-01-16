<?php

namespace App\Repositories;

use App\Models\File;
use Illuminate\Support\Facades\Auth;

class FileRepository extends GenericRepository
{
    public function __construct(){
        parent::__construct(new File());
    }

    public function getFilesByGroupId(int $groupId)
    {
        return File::whereGroupId($groupId)->whereIsAccepted(1)->get();
    }

    public function getFileRequestsByGroupIds($groupIds)
    {
        return File::whereIn('group_id', $groupIds)->whereIsAccepted(0)->get();
    }

    public function getFilesByIds(array $ids)
    {
        return File::whereIn('id', $ids)->whereIsAccepted(1)->get();
    }

    public function updateFile(File $file, array $data): bool
    {
        return $file->update($data);
    }

    public function deleteFile(File $file): bool
    {
        return $file->delete();
    }

    public function getFilesInStatus(array $ids, string $status, int $isAccepted)
    {
        return File::whereIn('id', $ids)
            ->whereIsAccepted(1)
            ->whereStatus($status)->get();
    }

    public function getReservedFilesByUser(int $userId, array $fileIds = [])
    {
        $query = File::whereIsAccepted(1)
            ->whereStatus('RESERVED')
            ->whereCurrentReserverId($userId);
        if (!empty($fileIds)) {
            $query->whereIn('id', $fileIds);
        }
        return $query->get();
    }

    public function getAutoCheckoutCandidates(string $status, $timeoutLimit)
    {
        return File::where('status', $status)
            ->where('check_in_time', '<=', $timeoutLimit)
            ->get();
    }

    public function getFilesGroupId($fileIds)
    {
        $files = File::whereIn('id', $fileIds)
            ->whereIsAccepted(1)->pluck('group_id')->toArray();
        $groupIds = array_unique($files);
        return sizeof($groupIds) > 1 ? false : $groupIds;
    }

    public function getByIds($fileIds, $withLock = 0)
    {
        $files = File::whereIn('id', $fileIds)
            ->where('is_accepted', 1);
        if ($withLock == 1) {
            $files->lockForUpdate();
        }

        return $files->get();
    }

    public function getUserCheckedInFiles()
    {
        $files =  File::whereIsAccepted(1)
            ->whereStatus('RESERVED')
            ->whereCurrentReserverId(Auth::id())
            ->get();

        return $files;
    }
}
