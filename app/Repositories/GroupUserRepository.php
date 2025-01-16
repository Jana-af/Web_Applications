<?php

namespace App\Repositories;

use App\Models\GroupUser;

class GroupUserRepository extends GenericRepository
{

    public function __construct(){
        parent::__construct(new GroupUser());
    }

    public function addUserToGroup(int $userId, int $groupId, bool $isOwner, bool $isAccepted): GroupUser
    {
        return GroupUser::create([
            'user_id' => $userId,
            'group_id' => $groupId,
            'is_owner' => $isOwner,
            'is_accepted' => $isAccepted,
        ]);
    }

    public function checkIfUserOwnsGroup(int $userId, int $groupId): bool
    {
        return GroupUser::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('is_owner', 1)
            ->exists();
    }

    public function getInvitesForGroup(int $groupId)
    {
        return GroupUser::where('group_id', $groupId)->get();
    }


    public function updateInvite(GroupUser $invite, array $data)
    {
        return $this->update($invite, $data);
    }

    public function findByGroupAndUser($groupId, $userId, $isOwner = null, $isAccepted = null)
    {
        $query = GroupUser::where('group_id', $groupId)
            ->where('user_id', $userId);

        if (!is_null($isOwner)) {
            $query->where('is_owner', $isOwner);
        }

        if (!is_null($isAccepted)) {
            $query->where('is_accepted', $isAccepted);
        }

        return $query->first();
    }

    public function getUserIdsInGroup($groupId)
    {
        return GroupUser::where('group_id', $groupId)
        ->whereIsAccepted(1)
        ->pluck('user_id')->toArray();
    }

    public function getGroupIdsForUser($userId, $isOwner = null)
    {
        $query = GroupUser::where('user_id', $userId);

        if (!is_null($isOwner)) {
            $query->where('is_owner', $isOwner);
        }

        return $query->pluck('group_id')->toArray();
    }

    public function getInvites($groupIds, $isAccepted = 0, $isOwner = 0)
    {
        return GroupUser::whereIn('group_id', $groupIds)
            ->where('is_owner', $isOwner)
            ->where('is_accepted', $isAccepted)
            ->get();
    }

    public function getUserInvites($userId, $isAccepted = 0)
    {
        return GroupUser::where('user_id', $userId)
            ->where('is_accepted', $isAccepted)
            ->get();
    }
}
