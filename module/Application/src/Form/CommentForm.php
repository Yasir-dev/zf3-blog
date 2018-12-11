<?php

namespace Application\Form;

use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

class CommentForm extends Form
{
    /**
     * CommentForm constructor.
     */
    public function __construct()
    {
        parent::__construct('contact-form');
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilters();
    }

    /**
     * Add elements to the form
     */
    private function addElements()
    {
        $author = (new Text())
            ->setName('author')
            ->setLabel('Author')
            ->setAttribute('id', 'author');
        $this->add($author);

        $comment = (new Textarea())
            ->setName('comment')
            ->setLabel('Content')
            ->setAttribute('id', 'comment');
        $this->add($comment);

        $submit = (new Submit())
            ->setName('submit')
            ->setAttribute('value', 'Save')
            ->setAttribute('id', 'submitButton');
        $this->add($submit);
    }

    /**
     * Add input filters and validators to form
     */
    private function addInputFilters()
    {
        $inputFilter = new InputFilter();

        $author = (new Input())->setName('author')->setRequired(true);
        $author->getFilterChain()->attach(new StringTrim());
        $author->getValidatorChain()->attach((new StringLength())->setMin(1)->setMax(128));
        $inputFilter->add($author);

        $comment = (new Input())->setName('comment')->setRequired(true);
        $comment->getFilterChain()->attach(new StripTags());
        $comment->getValidatorChain()->attach((new StringLength())->setMin(1)->setMax(4096));
        $inputFilter->add($author)->add($comment);

        $this->setInputFilter($inputFilter);
    }
}
