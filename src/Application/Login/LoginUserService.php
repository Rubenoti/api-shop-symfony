<?php

namespace App\Application\Login;

use App\Domain\User\User;
use App\Infrastructure\Doctrine\UserRepository;
use Exception;

class LoginUserService
{
    public function __construct(
        public readonly UserRepository $repository,
    ) {

    }

     public function validateUser(string $email, string $password): ?User
    {
        $user = $this->repository->findUser($email, $password);
        return $user;
    }
}