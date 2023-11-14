<?php

namespace App\Application\Register;

use App\Domain\User\User;
use App\Infrastructure\Doctrine\UserRepository;

class RegisterUserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly User $user
        ) {
    }

    public function createUser(string $email, string $password): void
    {
       $this->user->setEmail($email);
       $this->user->setPassword($password);

       $this->repository->createUser($this->user);
    }
}