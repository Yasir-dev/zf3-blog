<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * Class User
 * @package User\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * Active user status constant
     */
    const ACTIVE = 1;

    /**
     * Inactive user status constant
     */
    const INACTIVE = 0;

    /**
     * @var
     *
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var
     *
     * @ORM\Column(name="email")
     */
    private $email;

    /**
     * @var
     *
     * @ORM\Column(name="full_name")
     */
    private $fullName;

    /**
     * @var
     *
     * @ORM\Column(name="password")
     */
    private $password;

    /**
     * @var
     *
     * @ORM\Column(name="status")
     */
    private $status;

    /**
     * @var
     *
     * @ORM\Column(name="date_created")
     */
    private $dateCreated;

    /**
     * @var
     *
     * @ORM\Column(name="pwd_reset_token")
     */
    private $passwordResetToken;

    /**
     * @var
     *
     * @ORM\Column(name="pwd_reset_token_creation_date")
     */
    private $passwordResetTokenCreationDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

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
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * @param mixed $passwordResetToken
     * @return User
     */
    public function setPasswordResetToken($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * @param mixed $passwordResetTokenCreationDate
     * @return User
     */
    public function setPasswordResetTokenCreationDate($passwordResetTokenCreationDate)
    {
        $this->passwordResetTokenCreationDate = $passwordResetTokenCreationDate;

        return $this;
    }
}
