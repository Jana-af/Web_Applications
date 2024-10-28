<?php

namespace App\Services;

use App\Models\GroupUser;
use App\Models\User;

class UserService extends GenericService
{
    public function __construct()
    {
        parent::__construct(new User());
    }


    public function inviteUserToGroup($validatedData){
        GroupUser::create($validatedData);
    }
}
