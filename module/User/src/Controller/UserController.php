<?php
/**
 * @link      http://github.com/zendframework/Foo for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use User\Service\UserManager;
use Zend\Mvc\Controller\AbstractActionController;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * UserController constructor.
     * @param EntityManager $entityManager
     * @param UserManager $userManager
     */
    public function __construct(EntityManager $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    /**
     * Index action
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = $this->params()->fromQuery('page', -1);

        $query = $this->entityManager->getRepository(User::class)->findAllUsers();

        //pagination
        $adaptor = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = (new Paginator($adaptor))
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(5);

        return new ViewModel([
            'users' => $paginator
        ]);
    }
}
