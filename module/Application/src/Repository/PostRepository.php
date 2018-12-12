<?php

namespace Application\Repository;

use Application\Entity\Post;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PostRepository extends EntityRepository
{
    /**
     * Return the Query object (posts) with process DQL(Doctrine query language)
     *
     * @return Query
     */
    public function findPosts()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', 1);

        return $queryBuilder->getQuery();
    }

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
     * Return the Query object (find post by tag) with process DQL(Doctrine query language)
     *
     * @param string $tag
     *
     * @return Query
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

        return $queryBuilder->getQuery();
    }
}
