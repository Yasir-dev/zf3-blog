<?php

namespace User\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Session\SessionManager;

class AuthenticationManager
{
    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    public function __construct($authService, $sessionManager)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }

    public function login($email, $password, $rememberMe)
    {
        if (null === $this->authService->getIdentity()) {
            throw new \Exception('Already logged in');
        }

        /**
         * @var AuthenticationAdapter $authenticationAdaptor
         */
        $authenticationAdaptor = $this->authService->getAdapter();
        $authenticationAdaptor->setEmail($email);
        $authenticationAdaptor->setPassword($password);

        $result = $this->authService->authenticate();

        if (Result::SUCCESS === $result->getCode() && $rememberMe) {
            //cookie will expire in 30 days
            $this->sessionManager->rememberMe(60*60*24*30);
        }

        return $result;
    }

    public function logout()
    {
        if (null === $this->authService->getIdentity()) {
            throw new \Exception('The user is not logged in');
        }

        $this->authService->clearIdentity();
    }
}
