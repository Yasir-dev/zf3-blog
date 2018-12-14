<?php

namespace User\Service;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Crypt\Password\Bcrypt;

class UserManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addUser($data)
    {
        if ($this->userExists($data['email'])) {
            throw new \Exception("User with email address " . $data['$email'] . " already exists");
        }

        $passwordHash = (new Bcrypt())->create($data['password']);

        $user = (new User())
            ->setEmail($data['email'])
            ->setFullName($data['full_name'])
            ->setPassword($passwordHash)
            ->setStatus($data['status'])
            ->setDateCreated( date('Y-m-d H:i:s'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, $data)
    {
        if ($this->userExists($data['email'])) {
            throw new \Exception("User with email address " . $data['$email'] . " already exists");
        }

        $user->setEmail($data['email'])
            ->setFullName($data['full_name'])
            ->setStatus($data['status']);

        $this->entityManager->flush($user);
    }

    /**
     * @param User $user
     * @param $data
     * @return bool
     */##


    public function changePassword(User $user, $data)
    {
        if ($this->verifyPassword($user, $data['old_password'])) {
            $user->setPassword((new Bcrypt())->create($data['new_password']));
            $this->entityManager->flush($user);

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $password
     *
     * @return bool
     */
    public function verifyPassword(User $user, $password)
    {
        return (new Bcrypt())->verify($password, $user->getPassword());
    }

    /**
     * Check if user exists
     *
     * @param $email
     *
     * @return bool
     */
    private function userExists($email)
    {
        return (bool) $this->entityManager->getRepository(User::class)->findOneByEmail($email);
    }
}