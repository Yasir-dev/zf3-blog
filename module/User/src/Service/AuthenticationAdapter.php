<?php

namespace User\Service;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;

class AuthenticationAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return AuthenticationAdapter
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return AuthenticationAdapter
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;

        return $this;
    }

    public function authenticate()
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($this->email);

        if (null === $user) {
            return $this->getResultObject(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid login data']
            );
        }

        if (User::INACTIVE === $user->getStatus()){
            return $this->getResultObject(
                Result::FAILURE,
                null,
                ['User accout in not active']
            );
        }

        if ((new Bcrypt())->verify($this->password, $user->getPassword())){
            return $this->getResultObject(
                Result::SUCCESS,
                $this->email,
                ['Authentication successfull']
            );
        }

        return $this->getResultObject(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid login data']
        );
    }

    /**
     * @param int $resultCode
     * @param $identity
     * @param array $messages
     * @return Result
     */
    private function getResultObject(int $resultCode, $identity, array $messages)
    {
        return (new Result($resultCode, $identity, $messages));
    }
}
