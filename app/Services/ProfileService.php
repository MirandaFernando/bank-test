<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;

class ProfileService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfileData(Request $request)
    {
        return $request->user();
    }

    public function updateProfile(Request $request, array $validatedData)
    {
        return $this->userRepository->updateUser($request->user(), $validatedData);
    }

    public function deleteProfile(Request $request): void
    {
        $user = $request->user();

        $this->userRepository->logoutUser();
        $this->userRepository->deleteUser($user);
        $this->userRepository->invalidateSession($request);
    }
}
