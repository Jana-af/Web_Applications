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

        $this->middleware([
            'check.group.authority',
            'check.user.in.group'
        ])->only('inviteUserToGroup');

        parent::__construct(new UserRequest(), new UserResource([]), new UserService(new User()));
    }

    public function inviteUserToGroup(UserRequest $request)
    {
        $validatedData = $request->validated();

        $this->userService->inviteUserToGroup($validatedData);

        return $this->successResponse(
            $this->toResource(null, $this->resource),
            __('messages.invitationWasSentSuccessfully')
        );
    }
}
