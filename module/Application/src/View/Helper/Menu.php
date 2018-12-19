<?php


namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Menu extends AbstractHelper
{
    private $items = [];
    protected $activeItem;

    /**
     * @param array $items
     * @return Menu
     */
    public function setItems($items): Menu
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param mixed $activeItem
     * @return Menu
     */
    public function setActiveItem($activeItem)
    {
        $this->activeItem = $activeItem;

        return $this;
    }

    public function render()
    {
        $items = '';
        foreach ($this->items as $item) {
            $items .= $this->getItem($item);
        }

        return $items;
    }

    private function getItem($item)
    {
        if ($this->activeItem === $item['id']) {
            return \sprintf(
                '<li class="active">%s</li>',
                $this->getAnchorTag($item['link'], $item['label'])
            );
        }

        return \sprintf(
            '<li>%s</li>',
            $this->getAnchorTag($item['link'], $item['label'])
        );
    }

    private function getAnchorTag($link, $label)
    {
        return \sprintf('<a href="%s">%s</a>', $link, $label);
    }
}
