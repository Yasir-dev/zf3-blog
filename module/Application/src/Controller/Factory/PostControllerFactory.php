<?php

namespace Application\Controller\Factory;

use Application\Controller\PostController;
use Application\Service\PostManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PostControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PostController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(PostManager::class)
        );
    }
}