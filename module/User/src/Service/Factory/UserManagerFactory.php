<?php

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
       return new UserManager();
    }
}