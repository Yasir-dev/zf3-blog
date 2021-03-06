<?php
/**
 * @link      http://github.com/zendframework/Foo for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;
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

        $form = new PasswordChangeForm('change');
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

    public function resetPasswordAction()
    {
        $form = new PasswordResetForm();

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $user = $this->entityManager->getRepository(User::class)->findOneByEmail($data['email']);
                if (null !== $user && User::ACTIVE === (int) $user->getStatus()) {
                    $this->userManager->createPasswordResetToken($user);

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', ['action' => 'message', 'id' => 'sent']);
                }

                return $this->redirect()->toRoute('users', ['action'=> 'message', 'id' => 'invalid-email']);
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function messageAction()
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');

        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }

        return new ViewModel([
            'id' => $id
        ]);
    }

    public function setPasswordAction()
    {
        $email = $this->params()->fromQuery('email', null);
        $token = $this->params()->fromQuery('token', null);


        $this->validateToken($email, $token);

        $form = new PasswordChangeForm('reset');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $reset = $this->userManager->setNewPasswordViaToken($email, $token, $data['password']);
                if (true === $reset) {
                    return $this->redirect()->toRoute('users', ['action '=> 'message', 'id' => 'set']);
                }

                return $this->redirect()->toRoute('users', ['action' => 'message', 'id' => 'failed']);
            }
        }

        return new ViewModel(['form' => $form]);
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

    private function validateToken($email, $token)
    {
        // Validate token length
        if (null !== $token && (!is_string($token) || 32 !== strlen($token))) {
            throw new \Exception('Invalid token type or length');
        }

        if ($token === null ||
            !$this->userManager->validateToken($email, $token)) {
            return $this->redirect()->toRoute('users',
                ['action'=>'message', 'id'=>'failed']);
        }
    }
}
