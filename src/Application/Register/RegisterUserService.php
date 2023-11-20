<?php

namespace App\Application\Register;

use App\Domain\User\User;
use App\Infrastructure\Doctrine\UserRepository;

class RegisterUserService
{
    public function __construct(
        private UserRepository $repository,
        ) {
    }

    public function createUser(string $email, string $password): void
    {
       $user = new User();
       $user->setEmail($email);
       $user->setPassword($password);

       $this->repository->createUser($user);
    }
}