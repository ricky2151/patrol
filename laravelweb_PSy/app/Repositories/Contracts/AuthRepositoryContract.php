<?php

namespace App\Repositories\Contracts;

interface AuthRepositoryContract 
{
    public function login($credentials);

    // public function me();

    // public function logout();

    // public function refresh();

    public function canMePlayARole($role);
}