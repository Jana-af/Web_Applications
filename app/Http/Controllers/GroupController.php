<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Services\GroupService;
use App\Models\Group;

class GroupController extends GenericController
{
    private GroupService $groupService;


    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;

        parent::__construct(new GroupRequest(), new GroupResource([]), new GroupService(new Group()));
    }

    public function store(){

        $validatedData = request()->validate($this->request->rules());
        $this->groupService->store($validatedData);

        return $this->successResponse(
            $this->toResource(null, ''),
            __('messages.dataAddedSuccessfully')
        );

    }

    public function getMyGroups()
    {
        $validatedData = request()->validate($this->request->rules());
         $items = $this->groupService->getMyGroups($validatedData);

        return $this->successResponse(
            $this->toResource($items, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }
}
