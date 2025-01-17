<?php

namespace App\Services;

use App\Annotations\Transactional;
use App\Models\GroupUser;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService extends GenericService
{
    private GroupUserService $groupUserService;
    private GroupService $groupService;

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->groupUserService = new GroupUserService(new GroupUser());
        $this->userRepository = new UserRepository();
        $this->groupService = new GroupService();
        parent::__construct(new User(), $this->userRepository);
    }
    public function updateDeviceToken($validatedData)
    {
        return $this->update(
            [
                'device_token' => $validatedData['device_token']
            ],
            Auth::id()
        );
    }

    public function getAllUsers($validatedData)
    {
        $userIds = [];
        if (isset($validatedData['group_id'])) {
            $userIds = $this->groupUserService->getUserIdsInGroup($validatedData['group_id']);
        }
        return $this->userRepository->getUsersNotIn($userIds);
    }

    public function inviteUserToGroup($validatedData)
    {
        if ($this->groupUserService->checkUserIfInvited($validatedData['user_id'], $validatedData['group_id'])) {
            throw new Exception(__('messages.userAlreadyInvitedToGroup'), 401);
        }
        $this->groupUserService->store($validatedData);
    }

    public function getMyInvites($validatedData)
    {
        if (isset($validatedData['send']) && $validatedData['send'] == 1) {
            return $this->groupUserService->getSentInvites();
        } elseif (isset($validatedData['group_id'])) {
            return $this->groupUserService->getSentInvites($validatedData['group_id']);
        } else {
            return $validatedData['send'] ?: $this->groupUserService->getReceivedInvites();
        }
    }

    #[Transactional]
    public function acceptOrRejectOrCancelInvite($validatedData)
    {
        /**
         * @var GroupUser
         */
        $invite = $this->groupUserService->findById($validatedData['id']);

        if ($validatedData['action'] == 'cancel') {
            if ($invite->is_accepted != '0' || !$this->groupUserService->checkIfAuthUserOwnTheGroup($invite->group_id, Auth::user()->id)) {
                throw new \Exception("Invite not found !", 404);
            } else {
                $this->groupUserService->updateInvite($invite, ['is_accepted' => -2]);
            }
        } else {
            if ($invite->is_accepted != '0' || $invite->user_id != Auth::user()->id) {
                throw new \Exception("Invite not found !", 404);
            } else {
                switch ($validatedData['action']) {
                    case 'accept':
                        $this->groupUserService->updateInvite($invite, ['is_accepted' => 1]);
                        break;

                    case 'reject':
                        $this->groupUserService->updateInvite($invite, ['is_accepted' => -1]);
                        break;
                }
            }
        }
    }

    public function getUsersInGroup($validatedData)
    {
        if (
            !$this->groupUserService->checkUserInGroup(Auth::id(), $validatedData['id']) && Auth::user()->role == 'USER'
        ) {
            throw new Exception(__('messages.userDoesNotHavePermissionOnGroup'), 404);
        }
        $group = $this->groupService->findById($validatedData['id']);

        return $group->users()->wherePivot('is_accepted', 1)->get();
    }

    public function removeUserFromGroup($validatedData)
    {
        $this->groupUserService->removeUserFromGroup($validatedData['user_id'], $validatedData['group_id']);
    }

    public function store($validatedData)
    {
        $createdUser = $this->repository->create($validatedData);
        if ($validatedData['role'] == 'ADMIN') {
            $groupUserData = [
                'user_id' => $createdUser->id,
                'group_id' => 1,
                'is_owner' => 1,
                'is_accepted' => 1
            ];
            $this->groupUserService->store($groupUserData);
        }
        return $createdUser;
    }
}
