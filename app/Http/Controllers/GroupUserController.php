<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupUserRequest;
use App\Http\Resources\GroupUserResource;
use App\Services\GroupUserService;
use App\Models\GroupUser;

class GroupUserController extends GenericController
{
    private GroupUserService $groupUserService;


    public function __construct(GroupUserService $groupUserService)
    {
        $this->groupUserService = $groupUserService;
        
        parent::__construct(new GroupUserRequest(), new GroupUserResource([]), new GroupUserService(new GroupUser()));
    }
}
