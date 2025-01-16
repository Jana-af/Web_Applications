<?php

namespace App\Repositories;

use App\Models\Group;

class GroupRepository extends GenericRepository
{
    public function __construct(){
        parent::__construct(new Group());
    }

    public function createGroup(array $data): Group
    {
        return Group::create($data);
    }
}
