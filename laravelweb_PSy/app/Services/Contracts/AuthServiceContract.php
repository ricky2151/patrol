<?php

namespace App\Services\Contracts;

interface AuthServiceContract {
    
    public function login($request);

    public function isLogin();

    public function canMePlayARoleAsAdmin();

    public function canMePlayARoleAsGuard();

    public function canMePlayARoleAsSuperAdmin();

    public function logout();

    // public function refresh();
}