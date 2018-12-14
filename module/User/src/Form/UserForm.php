<?php

namespace User\Form;


use User\Entity\User;
use Zend\Filter\StringTrim;
use Zend\Filter\ToInt;
use Zend\Form\Element\Password;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\Validator\Identical;
use Zend\Validator\InArray;
use Zend\Validator\StringLength;

class UserForm extends Form
{
    private $formType;

    public function __construct($type = 'create')
    {
        $this->formType = $type;
        parent::__construct('user-form');
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilters();
    }

    private function addElements()
    {
        $email = (new Text())
            ->setName('email')
            ->setLabel('E-mail Address');
        $this->add($email);

        $email = (new Text())
            ->setName('full_name')
            ->setLabel('Full Name');
        $this->add($email);

        if ("create" == $this->formType) {
            $password = (new Password())
                ->setName('password')
                ->setLabel('Password');
            $this->add($password);

            $confirmPassword = (new Password())
                ->setName('confirm_password')
                ->setLabel('Confirm password');
            $this->add($confirmPassword);
        }

        $status = (new Select())
            ->setName('status')
            ->setValueOptions([User::ACTIVE, User::INACTIVE]);
        $this->add($status);

        $submit = (new Submit())
            ->setName('submit')
            ->setAttribute('value', 'Add');
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

        $fullName = (new Input())->setName('full_name')->setRequired(true);
        $fullName->getFilterChain()->attach(new StringTrim());
        $fullName->getValidatorChain()->attach((new StringLength())->setMin(1)->setMax(512));
        $inputFilter->add($fullName);

        if ("create" == $this->formType) {
            $password = (new Input())->setName('password')->setRequired(true);
            $password->getValidatorChain()->attach((new StringLength())->setMin(6)->setMax(64));
            $inputFilter->add($password);

            $ConfirmPassword = (new Input())->setName('confirm_password')->setRequired(true);
            $ConfirmPassword->getValidatorChain()->attach((new Identical())->setToken('password'));
            $inputFilter->add($ConfirmPassword);
        }

        $status = (new Input())->setName('status')->setRequired(true);
        $status->getFilterChain()->attach(new ToInt());
        $status->getValidatorChain()->attach((new InArray())->setHaystack([User::ACTIVE, User::INACTIVE]));
        $inputFilter->add($status);

        $this->setInputFilter($inputFilter);
    }
}
