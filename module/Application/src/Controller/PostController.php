<?php
namespace Application\Controller;

use Application\Entity\Post;
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


    public function editAction()
    {
        $form = new PostForm();
        $postId = $this->params()->fromRoute('id', -1);

        $post = $this->entityManager->getRepository(Post::class)
            ->findOneById($postId);

        if (null === $post) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $this->postManager->updatePost($form->getData(), $post);

                return $this->redirect()->toRoute('post', ['action' => 'admin']);
            }
        }

        else {
            $data = [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'tags' => $this->postManager->convertTagsToString($post),
                'status' => $post->getStatus()
            ];

            $form->setData($data);
        }

        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'post' => $post
        ]);
    }
}