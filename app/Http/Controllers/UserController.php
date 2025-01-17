<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\UserResource;
use App\Models\GroupUser;
use App\Services\UserService;
use App\Models\User;
use App\Services\GroupUserService;

class UserController extends GenericController
{
    private UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->middleware('role:ADMIN')->only(['store', 'getAll', 'getCount', 'removeUserFromGroup']);


        $this->middleware([
            'check.group.authority'
        ])->only('inviteUserToGroup');

        parent::__construct(new UserRequest(), new UserResource([]), new UserService( new GroupUserService(new GroupUser())));
    }

    public function getAllUsers(UserRequest $userRequest)
    {
        $validatedData = $userRequest->validated();
        $items = $this->userService->getAllUsers($validatedData);

        return $this->successResponse(
            $this->toResource($items, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
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

    public function getMyInvites(UserRequest $request){
        $validatedData = $request->validated();

        $model = $this->userService->getMyInvites($validatedData);

        return $this->successResponse(
            $this->toResource($model, GroupUserResource::class ),
            __('messages.dataFetchedSuccessfully')
        );
    }

    public function acceptOrRejectOrCancelInvite(UserRequest $request){
        $validatedData = $request->validated();

       $this->userService->acceptOrRejectOrCancelInvite($validatedData);
        switch($validatedData['action']){
            case'reject' :
                $key = 'messages.invitationRejectedSuccessfully';
            break;
            case'accept' :
                $key = 'messages.invitationAcceptedSuccessfully';
            break;
            case'cancel' :
                $key = 'messages.invitationCancelledSuccessfully';
            break;
        }
        return $this->successResponse(
            $this->toResource(null, '' ),
            __($key)
        );
    }

    public function getUsersInGroup(UserRequest $request){
        $validatedData = $request->validated();

        $model = $this->userService->getUsersInGroup($validatedData);

        return $this->successResponse(
            $this->toResource($model, $this->resource ),
            __('messages.dataFetchedSuccessfully')
        );
    }


    public function removeUserFromGroup(UserRequest $request){
        $validatedData = $request->validated();

        $this->userService->removeUserFromGroup($validatedData);

        return $this->successResponse(
            $this->toResource(null, '' ),
            __('messages.dataUpdatedSuccessfully')
        );
    }

}
