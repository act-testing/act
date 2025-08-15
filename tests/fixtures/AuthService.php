<?php

namespace Tests\Fixtures;

class AuthService
{
    public function addUser(string $email, string $password): void
    {
        //
    }

    public function attempt(string $email, string $password): bool
    {
        return true;
    }
}
