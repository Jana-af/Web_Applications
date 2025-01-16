<?php

namespace App\Services;


use App\Annotations\Logger;
use App\Annotations\Transactional;
use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;

class GroupService extends GenericService
{
    private GroupRepository $groupRepository;
    private GroupUserService $groupUserService;

    public function __construct()
    {
        $this->groupRepository = new GroupRepository();
        $this->groupUserService = new GroupUserService();
        parent::__construct(new Group(), $this->groupRepository);
    }

    #[Transactional]
    public function store($validatedData)
    {
        $group = $this->groupRepository->createGroup($validatedData);

        $this->groupUserService->addUserToGroup(
            Auth::user()->id,
            $group->id,
            true,
            true
        );
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
