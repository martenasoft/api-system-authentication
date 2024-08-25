<?php

namespace App\Security;

use App\Entity\User;
use App\Service\Interfaces\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserApiServiceProvider implements UserProviderInterface
{
    public function __construct(private UserServiceInterface $userService)
    {
    }
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException("Unsupported user class.");
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class)
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->userService->getUser();
    }
}
