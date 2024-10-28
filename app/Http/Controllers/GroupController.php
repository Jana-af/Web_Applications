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

    public function getMyGroups()
    {
         $items = $this->groupService->getMyGroups();

        return $this->successResponse(
            $this->toResource($items, $this->resource),
            __('messages.dataFetchedSuccessfully')
        );
    }
}
