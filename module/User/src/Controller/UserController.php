<?php
/**
 * @link      http://github.com/zendframework/Foo for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use User\Form\PasswordChangeForn;
use User\Form\UserForm;
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

    public function viewAction()
    {
        $user = $this->getUser($this->getUserId());

        if (null === $user) {
            $this->getResponse()->setStatusCode(404);
        }

        return new ViewModel([
            'user' => $user
        ]);
    }

    public function addAction()
    {
        $form = new UserForm();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $user = $this->userManager->addUser($form->getData());

                return $this->redirect()->toRoute('users', ['action' => 'view', 'id' => $user->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction()
    {
        $user = $this->getUser($this->getUserId());

        if (null === $user) {
            $this->getResponse()->setStatusCode(404);
        }

        $form = new UserForm('edit');
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $this->userManager->updateUser($user, $form->getData());

                return $this->redirect()->toRoute('users', ['action' => 'view', 'id' => $user->getId()]);
            }
        } else {
            $form->setData([
                'full_name' => $user->getFullName(),
                'email' => $user->getEmail(),
                'status' => $user->getStatus(),
            ]);
        }

        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }

    public function changePasswordAction()
    {
        $user = $this->getUser($this->getUserId());

        if (null === $user) {
            $this->getResponse()->setStatusCode(404);
        }

        $form = new PasswordChangeForn();
        if ($this->getRequest()->isPost()){
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                if ($this->userManager->changePassword($user, $form->getData())) {
                    $this->flashMessenger()->addSuccessMessage('password has been changed successfully!');
                } else {
                    $this->flashMessenger()->addErrorMessage('The old password is incorrect!');
                }
                    return $this->redirect()->toRoute('users', ['action' => 'view', 'id' => $user->getId()]);
            }
        }

        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * @return mixed
     */
    private function getUserId()
    {
        return $this->params()->fromRoute('id', -1);
    }

    /**
     * @param $id
     *
     * @return null|object
     */
    private function getUser($id)
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }
}
