<?php

namespace App\Service\Interfaces;

use App\Entity\User;

interface UserServiceInterface
{
    public function getUserId(string $email, string $password): int;
    public function getUser(): User;
}
