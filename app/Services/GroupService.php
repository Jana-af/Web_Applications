<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupService extends GenericService
{
    public function __construct()
    {
        parent::__construct(new Group());
    }

    public function store($validatedData)
    {
        DB::beginTransaction();

        $model = Group::create($validatedData);

        GroupUser::create([
            'user_id' => Auth::user()->id,
            'group_id' => $model->id,
            'is_owner' => 1,
            'is_accepted' => 1
        ]);


        DB::commit();
    }

    public function getMyGroups()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        return $user->groups()->wherePivot('is_accepted', 1)->get();
    }
}
