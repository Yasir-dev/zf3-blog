<?php

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\UserController;
use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return UserController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(UserManager::class)
        );
    }
}