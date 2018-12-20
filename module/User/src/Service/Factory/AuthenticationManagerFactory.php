<?php

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\AuthenticationManager;
use Zend\Authentication\AuthenticationService;
use Zend\Config\Config;
use Zend\Config\Factory;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class AuthenticationManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        return new AuthenticationManager(
            $container->get(AuthenticationService::class),
            $container->get(SessionManager::class),
            $config['access_filter']
        );
    }
}
