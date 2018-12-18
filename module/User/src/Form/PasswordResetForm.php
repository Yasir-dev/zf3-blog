<?php

namespace User\Form;

use Zend\Captcha\Figlet;
use Zend\Captcha\Image;
use Zend\Filter\StringTrim;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;

class PasswordResetForm extends Form
{
    public function __construct()
    {
        parent::__construct('password-reset-form');
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilter();
    }

    private function addElements()
    {
        $email = (new Text())
            ->setName('email')
            ->setLabel('E-Mail Address');
        $this->add($email);

        $captcha = new Captcha();
        $captcha->setName('captcha');

        $captcha->setCaptcha(new Figlet());
        $this->add($captcha);

        $csrf = (new Csrf())
            ->setName('csrf')
            ->setCsrfValidatorOptions(['timeout' => 600]);
        $this->add($csrf);

        $submit = (new Submit())
            ->setName('submit')
            ->setAttribute('value', 'Reset Password');
        $this->add($submit);
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $email = (new Input())->setName('email')->setRequired(true);
        $email->getFilterChain()->attach(new StringTrim());
        $email->getValidatorChain()->attach(
                (new EmailAddress())
                    ->setAllow(Hostname::ALLOW_DNS)
                    ->useMxCheck(false)
            );

        $inputFilter->add($email);

        $this->setInputFilter($inputFilter);
    }
}
