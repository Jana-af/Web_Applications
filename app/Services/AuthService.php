<?php

namespace App\Services;

use App\Annotations\Transactional;
use App\Models\GroupUser;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    public function login($validatedData)
    {
        $user = User::where('username', $validatedData['username'])->first();

        if (!$user) {
            throw new Exception(__('auth.failed'), 401);
        }

        $attemptedData = [
            'username'     => $user->username,
            'password'     => $validatedData['password']
        ];

        if (!$token = Auth::attempt($attemptedData)) {
            throw new Exception(__('auth.incorrect_password'), 401);
        }

        return $token;
    }

    #[Transactional]
    public function register($validatedData)
    {
        $attemptedData = [
            'username'    => $validatedData['username'],
            'password'    => $validatedData['password']
        ];

        $validatedData['role']          = 'USER';
        $validatedData['password']      = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        GroupUser::create([
            'user_id' => $user->id,
            'group_id' => 1,
            'is_owner' => 0,
            'is_accepted' => 1
        ]);

        return $this->login($attemptedData);
    }


    public function getUserProfile()
    {
        return Auth::user();
    }
}
