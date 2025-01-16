<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\GroupUser;

class UserRepository extends GenericRepository
{
    public function __construct()
    {
        parent::__construct(new User());
    }

    public function findByUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function assignGroup(int $userId, int $groupId, bool $isOwner = false, bool $isAccepted = true)
    {
        return GroupUser::create([
            'user_id'    => $userId,
            'group_id'   => $groupId,
            'is_owner'   => $isOwner,
            'is_accepted' => $isAccepted,
        ]);
    }

    public function getUsersNotIn(array $userIds)
    {
        return User::whereNotIn('id', $userIds)->get();
    }
}
