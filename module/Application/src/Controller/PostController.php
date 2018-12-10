<?php
namespace Application\Controller;

use Application\Form\PostForm;
use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PostController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PostManager
     */
    private $postManager;
    /**
     * @var PostManager
     */

    /**
     * PostController constructor.
     *
     * @param EntityManager $entityManager
     * @param PostManager $manager
     */
    public function __construct(EntityManager $entityManager, PostManager $manager)
    {
        $this->entityManager = $entityManager;
        $this->postManager   = $manager;
    }

    public function addAction()
    {
        $form = new PostForm();
        if ($this->getRequest()->isPost()){
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $this->postManager->addPost($form->getData());

                return $this->redirect()->toRoute('application');
            }
        }

        return new ViewModel(['form' => $form]);
    }
}