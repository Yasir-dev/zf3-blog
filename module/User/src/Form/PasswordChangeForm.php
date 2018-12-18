<?php

namespace User\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;

class PasswordChangeForm extends Form
{
    private $formType;

    public function __construct($type)
    {
        parent::__construct('password-change');
        $this->formType = $type;
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilters();
    }

    private function addElements()
    {
        if ('change' === $this->formType) {
            $oldPassword = (new Password())
                ->setName('old_password')
                ->setLabel('Old password');
            $this->add($oldPassword);
        }

        $newPassword = (new Password())
            ->setName('new_password')
            ->setLabel('New password');
        $this->add($newPassword);

        $confirmPassword = (new Password())
            ->setName('confirm_new_password')
            ->setLabel('Confirm new password');
        $this->add($confirmPassword);

        $csrf = (new Csrf())
            ->setName('csrf')
            ->setCsrfValidatorOptions(['timeout' => 600]);
        $this->add($csrf);

        $submit = (new Submit())
            ->setName('submit')
            ->setAttribute('value', 'Change Password');
        $this->add($submit);
    }

    private function addInputFilters()
    {
        $inputFilter = new InputFilter();

        if ($this->scenario == 'change') {
            $OldPassword = (new Input())->setName('old_password')->setRequired(true);
            $OldPassword->getValidatorChain()->attach((new StringLength())->setMin(6)->setMax(64));
            $inputFilter->add($OldPassword);
        }

        $newPassword = (new Input())->setName('new_password')->setRequired(true);
        $newPassword->getValidatorChain()->attach((new StringLength())->setMin(6)->setMax(64));
        $inputFilter->add($newPassword);

        $ConfirmPassword = (new Input())->setName('confirm_new_password')->setRequired(true);
        $ConfirmPassword->getValidatorChain()->attach((new Identical())->setToken('new_password'));
        $inputFilter->add($ConfirmPassword);

        $this->setInputFilter($inputFilter);
    }
}
