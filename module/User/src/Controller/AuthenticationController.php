<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;


class AuthenticationController extends AbstractActionController
{
    private $entityManager;
    private $userManager;
    private $authenticationManager;

    public function __construct($entityManager, $userManager, $authManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->authenticationManager = $authManager;
    }

    public function loginAction()
    {

    }

    public function logoutAction()
    {

    }
}
