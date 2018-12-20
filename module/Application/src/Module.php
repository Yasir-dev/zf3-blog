<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use User\Controller\AuthenticationController;
use User\Service\AuthenticationManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();
        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            100
        );
    }


    public function onDispatch(MvcEvent $event)
    {
        $controllerObject = $event->getTarget();
        $controller = $event->getRouteMatch()->getParam('controller', null);
        $action  = $event->getRouteMatch()->getParam('action', null);

        /**
         * @var AuthenticationManager $authenticationManager
         */
        $authenticationManager = $event->getApplication()->getServiceManager()->get(AuthenticationManager::class);

        if (AuthenticationController::class !== $controller && false === $authenticationManager->accessFilter($controller, $action)) {

            $uri = $event->getApplication()->getRequest()->getUri();
            //Make the URL relative to avoid redirecting to other domain by a malicious user
            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);

            $redirectUrl = $uri->toString();

            // Redirect the user to the "Login" page.
            return $controllerObject->redirect()->toRoute('login', [], ['query' => ['redirectUrl'=> $redirectUrl]]);
        }
    }
}
