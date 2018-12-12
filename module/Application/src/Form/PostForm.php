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

        $title = (new Input())
            ->setName('title')
            ->setRequired(true);

        $title->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags())
            ->attach(new StripNewlines());

        $title->getValidatorChain()
            ->attach((new StringLength())->setMin(1)->setMax(1024));

        $inputFilter->add($title);


        $content = (new Input())
            ->setRequired(true)
            ->setName('content');

         $content->getFilterChain()
             ->attach(new StringTrim());
         $content->getValidatorChain()
             ->attach((new StringLength())->setMin(1)->setMax(1024));

        $inputFilter->add($content);

        $tags = (new Input())
            ->setName('tags')
            ->setRequired(true);

        $tags->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags())
            ->attach(new StripNewlines());

        $tags->getValidatorChain()
            ->attach((new StringLength())->setMin(1)->setMax(1024));

        $inputFilter->add($tags);

        $status = (new Input())
            ->setName('status')
            ->setRequired(true);

        $status->getValidatorChain()
            ->attach((new InArray())->setHaystack(Post::STATUS));

        $inputFilter->add($status);

        $this->setInputFilter($inputFilter);
    }
}
