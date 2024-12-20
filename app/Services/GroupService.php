<?php

namespace App\Services;


use App\Annotations\Logger;
use App\Annotations\Transactional;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GroupService extends GenericService
{
    public function __construct()
    {
        parent::__construct(new Group());
    }

    #[Logger]
    #[Transactional]
    public function store($validatedData)
    {
        $model = Group::create($validatedData);

        GroupUser::create([
            'user_id' => Auth::user()->id,
            'group_id' => $model->id,
            'is_owner' => 1,
            'is_accepted' => 1
        ]);
    }

    public function getMyGroups($validatedData)
    {
        if(isset($validatedData['is_owner']) && $validatedData['is_owner']){
            /**
             * @var User $user
             */
            $user = Auth::user();
            return $user->groups()->wherePivot('is_accepted', 1)->wherePivot('is_owner',1)->get();
        }else{
            /**
             * @var User $user
             */
            $user = Auth::user();
            return $user->groups()->wherePivot('is_accepted', 1)->wherePivot('is_owner',0)->get();
        }

    }
}
