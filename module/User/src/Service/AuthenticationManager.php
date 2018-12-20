<?php

namespace User\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Form\Factory;
use Zend\Session\SessionManager;

class AuthenticationManager
{
    /**
     * Allow all users
     */
    const ALLOW_ALL = '*';

    /**
     * Allow only authorised user
     */
    const ALLOW_AUTHORISED = '@';

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var object
     */
    private $config;

    /**
     * @var array
     */
    private $accessModes = ['restrictive', 'permissive'];

    public function __construct($authService, $sessionManager, $config)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->config =  $config;
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

    public function accessFilter($controller, $action)
    {
        $this->verifyAccessMode($this->getAccessMode());

        $controllerConfig = $this->getAccessFilterControllerConfig($controller);
        $actions          = $this->getAccessFilterItem($controllerConfig, 'actions');
        $accessLevel      = $this->getAccessFilterItem($controllerConfig, 'allow');

        if (\in_array($action, $actions) && self::ALLOW_ALL === $accessLevel) {
            return true;
        }

        if (\in_array($action, $actions) && self::ALLOW_AUTHORISED === $accessLevel && $this->authService->getIdentity()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function getAccessMode()
    {
        return $this->config['options']['mode'] ?? 'restrictive';
    }

    /**
     * @param $mode
     * @throws \Exception
     */
    private function verifyAccessMode($mode)
    {
        if (!\in_array($mode, $this->accessModes)) {
            throw new \Exception('Invalid access filter mode');
        }
    }

    /**
     * @param $controller
     * @return mixed
     */
    private function getAccessFilterControllerConfig($controller)
    {
        return $this->config['controllers'][$controller];
    }

    /**
     * @param $array
     * @param $item
     * @return mixed
     */
    private function getAccessFilterItem($array, $item)
    {
        return \array_shift(\array_column($array, $item));
    }
}
