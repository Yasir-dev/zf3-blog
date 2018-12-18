<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Breadcrumb extends AbstractHelper
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @param array $items
     * @return Menu
     */
    public function setItems($items): Breadcrumb
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Return html for breadcrumb
     *
     * @return string
     */
    public function render()
    {
        $items = '';
        foreach ($this->items as $label => $link) {
            $items .= $this->getItem($label, $link);
        }

        return \sprintf('<ol class="breadcrumb">%s</ol>', $items);
    }

    /**
     * Return html for single breadcrumb item
     *
     * @param $link
     * @param $label
     *
     * @return string
     */
    private function getItem($label, $link)
    {
        if ($link === end($this->items)) {
            return \sprintf('<li class="active">%s</li>', $label);
        }

        return \sprintf('<li><a href="%s">%s</a></li>', $link, $label);
    }
}
