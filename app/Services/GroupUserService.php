<?php

namespace App\Services;

use App\Models\GroupUser;
use App\Repositories\GroupUserRepository;
use Illuminate\Support\Facades\Auth;

class GroupUserService extends GenericService
{

    private GroupUserRepository $groupUserRepository;

    public function __construct()
    {
        $this->groupUserRepository = new GroupUserRepository();
        parent::__construct(new GroupUser(), $this->groupUserRepository);
    }
    public function checkIfAuthUserOwnTheGroup($groupId, $userId)
    {
        return $this->groupUserRepository->findByGroupAndUser($groupId, $userId, 1) != null;
    }

    public function checkUserInGroup($userId, $groupId)
    {
        return $this->groupUserRepository->findByGroupAndUser($groupId, $userId, null, 1) != null;
    }

    public function getUserIdsInGroup($groupId)
    {
        return $this->groupUserRepository->getUserIdsInGroup($groupId);
    }

    public function myGroupIds()
    {
        return $this->groupUserRepository->getGroupIdsForUser(Auth::id(), 1);
    }

    public function getSentInvites($groupId = null)
    {
        $myGroups = $this->myGroupIds();

        if ($groupId !== null) {
            if (!in_array($groupId, $myGroups)) {
                return null;
            }
            return $this->groupUserRepository->getInvites([$groupId]);
        }

        return $this->groupUserRepository->getInvites($myGroups);
    }

    public function getReceivedInvites()
    {
        return $this->groupUserRepository->getUserInvites(Auth::id());
    }

    public function addUserToGroup(int $userId, int $groupId, bool $isOwner, bool $isAccepted): GroupUser
    {
        return $this->groupUserRepository->addUserToGroup($userId, $groupId, $isOwner, $isAccepted);
    }
    public function updateInvite(GroupUser $invite, array $data)
    {
        return $this->groupUserRepository->updateInvite($invite, $data);
    }
}
