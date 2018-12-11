<?php

namespace Application\Repository;

use Application\Entity\Post;
use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    /**
     * Find post having tags
     *
     * @return mixed
     */
    public function findPostsWithTag()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', 1);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find posts having a given tag
     *
     * @param string $tag
     *
     * @return mixed
     */
    public function findPostsByTag(string $tag)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->andWhere('t.name = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', 1)
            ->setParameter('2', $tag);

        return $queryBuilder->getQuery()->getResult();
    }
}
