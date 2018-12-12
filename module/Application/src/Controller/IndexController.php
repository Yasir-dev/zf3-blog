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
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
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
        //get query object from doctrin entity repo
        /**
         * @var Query $query
         */
        $query = $this->entityManager->getRepository(Post::class)
            ->findPosts();

        //get query parameters
        $tag = $this->params()->fromQuery('tag', null);
        $page = $this->params()->fromQuery('page', 1);

        if ($tag) {
            $query = $this->entityManager->getRepository(Post::class)
                ->findPostsByTag($tag);
        }

        //set pagination
        $adapter   = new DoctrineAdapter((new ORMPaginator($query, false)));
        $paginator = (new Paginator($adapter))
            ->setItemCountPerPage(4)
            ->setCurrentPageNumber($page);

        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->postManager
        ]);
    }
}
