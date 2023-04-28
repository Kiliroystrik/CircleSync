<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;

class GroupManagerService
{
    private $groupRepository;
    private $userRepository;


    public function __construct(GroupRepository $groupRepository, UserRepository $userRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    public function joinGroup(int $groupeId, User $user): void
    {
        $group = $this->groupRepository->find($groupeId);
        $user->addGroupe($group);
        $this->userRepository->save($user, true);
    }

    public function leaveGroup(int $groupeId, User $user): void
    {
        $group = $this->groupRepository->find($groupeId);
        $user->removeGroupe($group);
        $this->userRepository->save($user, true);
    }
}
