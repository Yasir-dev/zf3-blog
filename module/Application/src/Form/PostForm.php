<?php

namespace Application\Form;

use Application\Entity\Post;
use Zend\Filter\FilterChain;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Filter\StripTags;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\InArray;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class PostForm extends Form
{

    public function __construct()
    {
        parent::__construct('post-form');

        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilters();
    }

    private function addElements()
    {
        $title = (new Text())
            ->setName('title')
            ->setLabel('Title')
            ->setAttribute('id', 'title');

        $content = (new Textarea())
            ->setName('content')
            ->setLabel('Content')
            ->setAttribute('id', 'content');

        $tags = (new Text())
            ->setName('tags')
            ->setLabel('Tags')
            ->setAttribute('id', 'tags');

        $status = (new Select())
            ->setName('status')
            ->setLabel('Status')
            ->setAttribute('id', 'status')
            ->setValueOptions(Post::STATUS);

        $submit = (new Submit())
            ->setName('submit')
            ->setAttribute('value', 'Create')
            ->setAttribute('id', 'submitButton');

        $this->add($title)
            ->add($content)
            ->add($tags)
            ->add($status)
            ->add($submit);
    }

    private function addInputFilters()
    {
        $inputFilter = (new InputFilter());

        $filter = (new FilterChain())
            ->attach(new StringTrim())
            ->attach(new StripTags())
            ->attach(new StripNewlines());

        $validator = (new ValidatorChain());
        $validator->attach(
            (new StringLength())->setMin(1)->setMax(1024)
        );

        $title = (new Input())
            ->setName('title')
            ->setRequired(true)
            ->setFilterChain($filter)
            ->setValidatorChain($validator);

        $content = (new Input())
            ->setRequired(true)
            ->setName('content')
            ->setFilterChain((new FilterChain())->attach(new StringTrim()))
            ->setValidatorChain((new ValidatorChain())->attach((new StringLength())->setMin(1)->setMax(1024)));


        $tags = (new Input())
            ->setName('tags')
            ->setRequired(true)
            ->setFilterChain($filter)
            ->setValidatorChain($validator);

        $status = (new Input())
            ->setName('status')
            ->setRequired(true)
            ->setValidatorChain((new ValidatorChain())->attach((new InArray())->setHaystack(Post::STATUS)));

        $inputFilter->add($title)
            ->add($content)
            ->add($tags)
            ->add($status);

        $this->setInputFilter($inputFilter);
    }
}
