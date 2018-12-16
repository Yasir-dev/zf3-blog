<?php

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\AuthenticationController;
use User\Service\AuthenticationManager;
use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthenticationControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthenticationController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(UserManager::class),
            $container->get(AuthenticationManager::class)
        );
    }
}
