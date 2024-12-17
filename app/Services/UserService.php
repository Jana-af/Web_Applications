<?php

namespace App\Services;

use App\Annotations\Transactional;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService extends GenericService
{
    private GroupUserService $groupUserService;

    public function __construct()
    {
        $this->groupUserService = new GroupUserService(new GroupUser());
        parent::__construct(new User());
    }

    public function getAllUsers($validatedData)
    {
        $userIds = [];
        if (isset($validatedData['group_id'])) {
            $userIds = $this->groupUserService->getUserIdsInGroup($validatedData['group_id']);
        }
        return User::whereNotIn('id', $userIds)->get();
    }
    #[Transactional]
    public function inviteUserToGroup($validatedData)
    {
        GroupUser::create($validatedData);
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

        $invite = GroupUser::whereId($validatedData['id'])->first();

        if ($validatedData['action'] == 'cancel') {
            if ($invite->is_accepted != '0' || !$this->groupUserService->checkIfAuthUserOwnTheGroup(Auth::user()->id, $invite->group_id)) {
                throw new \Exception("Invite not found !", 404);
            } else {
                $invite->is_accepted = -2;
                $invite->save();
            }
        } else {
            if ($invite->is_accepted != '0' || $invite->user_id != Auth::user()->id) {
                throw new \Exception("Invite not found !", 404);
            } else {
                switch ($validatedData['action']) {
                    case 'accept':
                        $invite->is_accepted = 1;
                        $invite->save();
                        break;

                    case 'reject':
                        $invite->is_accepted = -1;
                        $invite->save();
                        break;
                }
            }
        }
    }

    public function getUsersInGroup($validatedData)
    {
        if (
            !$this->groupUserService->checkUserInGroup(Auth::id(), $validatedData['id'])
        ) {
            throw new Exception(__('messages.userDoesNotHavePermissionOnGroup'), 404);
        }
        return Group::whereId($validatedData['id'])->first()->users()->wherePivot('is_accepted', 1)->get();
    }
}
