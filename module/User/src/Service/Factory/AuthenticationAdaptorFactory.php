<?php

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\AuthenticationAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthenticationAdaptorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthenticationAdapter($container->get('doctrine.entitymanager.orm_default'));
    }
}