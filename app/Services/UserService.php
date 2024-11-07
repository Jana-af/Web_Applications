<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService extends GenericService
{
    private GroupUserService $groupUserService; //ToDo
    public function __construct(GroupUserService $groupUserService)
    {
        $this->groupUserService = $groupUserService;
        parent::__construct(new User());
    }


    public function inviteUserToGroup($validatedData){
        GroupUser::create($validatedData);
    }

    public function getMyInvites($validatedData){
        if($validatedData['send']){
            $myGroups = GroupUser::whereUserId(Auth::user()->id)->whereIsOwner(1)->pluck('group_id');

            $model = GroupUser::whereIn('group_id',$myGroups)->whereIsOwner(0)->whereIsAccepted(0)->get();
        }else{
            $model = GroupUser::whereUserId(Auth::user()->id)->whereIsAccepted(0)->get();
        }
        return $model;
    }

    public function acceptOrRejectOrCancelInvite($validatedData){

        $invite = GroupUser::whereId($validatedData['id'])->first();

        if($validatedData['action'] == 'cancel'){
            if($invite->is_accepted != '0' || !$this->groupUserService->checkIfAuthUserOwnTheGroup(Auth::user()->id,$invite->group_id)){
                throw new \Exception("Invite not found !", 404);
            }else{
                $invite->is_accepted = -2;
                $invite->save();
            }
        }else{
            if($invite->is_accepted != '0' || $invite->user_id != Auth::user()->id){
                throw new \Exception("Invite not found !", 404);
            }else{
                switch($validatedData['action']){
                    case'accept' :
                        $invite->is_accepted = 1;
                        $invite->save();
                    break;

                    case'reject' :
                        $invite->is_accepted = -1;
                        $invite->save();
                    break;
                }
            }
        }
        return $validatedData['action'];
    }

    public function getUsersInGroup($validatedData){
        $model = Group::whereId($validatedData['id'])->first()->users()->wherePivot('is_accepted', 1)->get();
        return $model;
    }
}
