<?php

namespace App\Services;

use App\Models\GroupUser;
use Illuminate\Support\Facades\Auth;

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
            ->whereUserId($user_id)->where('is_accepted', '!=', -1)->first() != null;
    }

    public function getUserIdsInGroup($group_id)
    {
        return GroupUser::whereGroupId($group_id)->pluck('user_id')->toArray();
    }

    public function myGroupIds()
    {
        return GroupUser::whereUserId(Auth::id())->whereIsOwner(1)->pluck('group_id')->toArray();
    }

    public function getSentInvites($group_id = null)
    {
        $myGroups = $this->myGroupIds();
        $query = null;
        if ($group_id != null) {
            if (in_array($group_id, $myGroups))
                $query = GroupUser::where('group_id', $group_id);
            else
                return null;
        } else {
            $query =  GroupUser::whereIn('group_id', $myGroups);
        }

        return $query->whereIsOwner(0)->whereIsAccepted(0)->get();
    }

    public function getReceivedInvites()
    {
        return GroupUser::whereUserId(Auth::user()->id)->whereIsAccepted(0)->get();
    }
}
