<?php

namespace App\Services;

use App\Annotations\Transactional;
use Exception;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function login($validatedData)
    {
        $user = $this->userRepository->findByUsername($validatedData['username']);

        if (!$user) {
            throw new Exception(__('auth.failed'), 401);
        }

        $attemptedData = [
            'username' => $user->username,
            'password' => $validatedData['password']
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
            'username' => $validatedData['username'],
            'password' => $validatedData['password']
        ];

        $validatedData['role'] = 'USER';
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = $this->userRepository->createUser($validatedData);

        $this->userRepository->assignGroup($user->id, 1);

        return $this->login($attemptedData);
    }

    public function getUserProfile()
    {
        return Auth::user();
    }
}
