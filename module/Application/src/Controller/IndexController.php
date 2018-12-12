<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\Post;
use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
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
     * IndexController constructor.
     */
    public function __construct(EntityManager $entityManager, PostManager $postManager)
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;
    }

    public function indexAction()
    {
        $posts = $this->entityManager->getRepository(Post::class)
            ->findBy(['status'=> 1],
                ['dateCreated'=>'DESC']);

        $tag = $this->params()->fromQuery('tag', null);

        if ($tag) {
            $posts = $this->entityManager->getRepository(Post::class)
                ->findPostsByTag($tag);
        }

        return new ViewModel([
            'posts' => $posts,
            'postManager' => $this->postManager
        ]);
    }
}
