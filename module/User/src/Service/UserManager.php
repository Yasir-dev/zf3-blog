<?php

namespace User\Service;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Zend\Mail\Message as MailMessage;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class UserManager
{
    const PASSWORD_RESET_TOKEN_CHAR_LIST = '0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * PHP template renderer.
     */
    private $viewRenderer;

    /**
     * Application config.
     */
    private $config;

    public function __construct(EntityManager $entityManager, $viewRenderer, $config)
    {
        $this->entityManager = $entityManager;
        $this->viewRenderer = $viewRenderer;
        $this->config = $config;
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
     */
    public function changePassword(User $user, $data)
    {
        if ($this->verifyPassword($data['old_password'], $user->getPassword())) {
            $user->setPassword((new Bcrypt())->create($data['new_password']));
            $this->entityManager->flush($user);

            return true;
        }

        return false;
    }

    public function createPasswordResetToken(User $user)
    {
        if (User::ACTIVE !== $user->setStatus()) {
            throw new \Exception('User account is not active ' . $user->getEmail());
        }

        $token = Rand::getString(32, self::PASSWORD_RESET_TOKEN_CHAR_LIST);
        $tokenHash = (new Bcrypt())->create($token);
        $user->setPasswordResetToken($tokenHash);
        $user->setPasswordResetTokenCreationDate(date('Y-m-d H:i:s'));

        $this->entityManager->flush();
        $this->sendResetPasswordEmail($user, $token);
    }

    /**
     * @param User $user
     * @param $password
     *
     * @return bool
     */
    public function verifyPassword($password, $passwordHash)
    {
        return (new Bcrypt())->verify($password, $passwordHash);
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

    private function sendResetPasswordEmail(User $user, $token)
    {
        // Send an email to user.
        $subject = 'Password Reset';

        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password?token=' . $token . "&email=" . $user->getEmail();

        // Produce HTML of password reset email
        $bodyHtml = $this->viewRenderer->render(
            'user/email/reset-password-email',
            [
                'passwordResetUrl' => $passwordResetUrl,
            ]);

        $html = new MimePart($bodyHtml);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $mail = new MailMessage();
        $mail->setEncoding('UTF-8');
        $mail->setBody($body);
        $mail->setFrom('no-reply@example.com', 'User Demo');
        $mail->addTo($user->getEmail(), $user->getFullName());
        $mail->setSubject('Password Reset');

        // Setup SMTP transport
        $transport = new SmtpTransport();
        $options   = new SmtpOptions($this->config['smtp']);
        $transport->setOptions($options);
        $transport->send($mail);
    }

    public function setNewPasswordViaToken($email, $token, $password)
    {
        if (!$this->validateToken($email, $token)) {
            return false;
        }

        /**
         * @var User $user
         */
        $user = $this->getUser($email);
        $user->setPassword((new Bcrypt())->create($password));
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);

        $this->entityManager->flush();

        return true;
    }

    public function validateToken($email, $token)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser($email);

        if (null !== $user || User::ACTIVE !== $user->getStatus()) {
            return false;
        }

        if ($this->verifyPassword($token, $user->getPasswordResetToken())) {
            return true;
        }

        if (\strtotime('now') - $user->getPasswordResetTokenCreationDate() > 24*60*60) {
            return false; // expired
        }

        return true;
    }

    /**
     * @param $email
     * @return mixed
     */
    private function getUser($email)
    {
        return $this->entityManager->getRepository(User::class)->findOneByEmail($email);
    }

}