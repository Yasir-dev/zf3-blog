<?php

namespace Application\Service\Factory;

use Application\Service\PostManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PostManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PostManager($container->get('doctrine.entitymanager.orm_default'));
    }
}