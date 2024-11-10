<?php

namespace App\Services;

use App\Models\GroupUser;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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


    public function register($validatedData)
    {
        if(User::where('username',$validatedData['username'])->first() != null){
            throw new Exception('Username already exists' , 400);
        }
        $attemptedData = [
            'username'    => $validatedData['username'],
            'password'         => $validatedData['password']
        ];

        $validatedData['role']          = 'USER';
        $validatedData['password']      = Hash::make($validatedData['password']);

        DB::beginTransaction();

        $user = User::create($validatedData);

        GroupUser::create([
            'user_id' => $user->id,
            'group_id' => 1,
            'is_owner' => 0,
            'is_accepted' => 1
        ]);

        DB::commit();

        return $this->login($attemptedData);
    }


    public function getUserProfile()
    {
        return Auth::user();
    }
}
