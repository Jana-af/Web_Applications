<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Models\User;

class UserController extends GenericController
{
    private UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        
        parent::__construct(new UserRequest(), new UserResource([]), new UserService(new User()));
    }
}
