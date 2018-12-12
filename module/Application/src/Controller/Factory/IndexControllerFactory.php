<?php


namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Service\PostManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IndexController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new IndexController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(PostManager::class)
        );
    }
}
