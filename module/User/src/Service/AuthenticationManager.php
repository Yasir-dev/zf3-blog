<?php

namespace User\Service;

class AuthenticationManager
{
    private $authService;
    private $sessionManager;

    public function __construct($authService, $sessionManager)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }
}