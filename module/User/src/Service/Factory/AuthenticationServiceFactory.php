<?php

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\AuthenticationAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authenticationStorage = new SessionStorage('Zend_Auth', 'session', $sessionManager);
        $authenticationAdapter = $container->get(AuthenticationAdapter::class);

        return new AuthenticationService($authenticationStorage, $authenticationAdapter);
    }
}