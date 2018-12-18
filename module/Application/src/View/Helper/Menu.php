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
        $result = '';
        foreach ($this->items as $item) {
            $id = isset($item['id']) ? $item['id'] : '';
            $isActive = ($id == $this->activeItem);

            $label = isset($item['label'])   ? $item['label'] : '';
            $link =  isset($item['link']) ? $item['link'] : '#';

            $result .= '<li class="' . ($isActive?'active':'') . '">';
            $result .= '<a href="'.$link.'">'.$label.'</a>';
            $result .= '</li>';
        }

        return $result;
    }
}
