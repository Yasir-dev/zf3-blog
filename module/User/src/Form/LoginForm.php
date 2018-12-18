<?php

namespace User\Form;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\Validator\InArray;
use Zend\Validator\StringLength;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login-form');
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilters();
    }

    private function addElements()
    {
        $email = (new Text())
            ->setName('email')
            ->setLabel('E-Mail Address');
        $this->add($email);

        $password = (new Password())
            ->setName('password')
            ->setLabel('Password');
        $this->add($password);

        $rememberMe = (new Checkbox())
            ->setName('remember_me')
            ->setLabel('Remember me');
        $this->add($rememberMe);

        $redirectUrl = (new Hidden())
            ->setName('redirect_url');
        $this->add($redirectUrl);

        $csrf = (new Csrf())
            ->setName('csrf')
            ->setCsrfValidatorOptions(['timeout' => 600]);
        $this->add($csrf);

        $submit = (new Submit())
            ->setName('submit')
            ->setAttribute('value', 'Login');
        $this->add($submit);
    }

    private function addInputFilters()
    {
        $inputFilter = new InputFilter();

        $email = (new Input())->setName('email')->setRequired(true);
        $email->getFilterChain()->attach((new StringTrim()));
        $email->getValidatorChain()->attach(
            (new EmailAddress())
                ->setAllow(Hostname::ALLOW_DNS)
                ->useMxCheck(false)
        );

        $inputFilter->add($email);

        $password = (new Input())->setName('password')->setRequired(true);
        $password->getValidatorChain()->attach((new StringLength())->setMin(6)->setMax(64));
        $inputFilter->add($password);

        $rememberMe = (new Input())->setName('remember_me')->setRequired(false);
        $rememberMe->getValidatorChain()->attach((new InArray())->setHaystack([0, 1]));
        $inputFilter->add($rememberMe);

        $redirectUrl = (new Input())->setName('redirect_url')->setRequired(false);
        $redirectUrl->getFilterChain()->attach(new StringTrim());
        $redirectUrl->getValidatorChain()->attach((new StringLength())->setMin(0)->setMax(2048));

        $this->setInputFilter($inputFilter);
    }
}
