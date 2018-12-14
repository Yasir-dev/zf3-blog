<?php

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use User\Entity\User;

class UserRepository extends EntityRepository
{
    /**
     * Return Query object (find all users)
     *
     * @return Query
     */
    public function findAllUsers()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.dateCreated', 'DESC');

        return $queryBuilder->getQuery();
    }
}