<?php

namespace App\Services;

use App\Models\GroupUser;

class GroupUserService extends GenericService
{
    public function checkIfAuthUserOwnTheGroup($user_id, $group_id)
    {
        return GroupUser::whereGroupId($group_id)
            ->whereUserId($user_id)->whereIsOwner(1)->first() != null;
    }

    public function checkUserInGroup($user_id, $group_id)
    {
        return GroupUser::whereGroupId($group_id)
            ->whereUserId($user_id)->whereIsAccepted('!=', -1)->first() != null;
    }
}
