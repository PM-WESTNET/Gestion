<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 21/11/16
 * Time: 8:14
 */

namespace app\components\menu;

class Menu {
    const MENU_TYPE_ROOT     = 'root';
    const MENU_TYPE_DIVIDER  = 'divider';
    const MENU_TYPE_ITEM     = 'item';

    const MENU_POSITION_FIRST = 'first';
    const MENU_POSITION_LAST = 'last';
    const MENU_POSITION_BEFORE = 'before';
    const MENU_POSITION_AFTER = 'after';

    /**
     * @var $sub_items
     */
    private $sub_items = [];

    /**
     * @var string
     */
    protected $type = Menu::MENU_TYPE_ITEM;
    /**
     * @var $name
     */
    private $name;

    /**
     * @var $label
     */
    protected $label = null;

    /**
     * @var bool
     */
    protected $visible = true;

    /**
     * @var array
     */
    protected $url = null;
    /**
     * @var $extra_data mixed
     */
    protected $extra_data = null;

    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Menu
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Menu
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     * @return Menu
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtraData()
    {
        return $this->extra_data;
    }

    /**
     * @param mixed $extra_data
     * @return Menu
     */
    public function setExtraData($extra_data)
    {
        $this->extra_data = $extra_data;
        return $this;
    }

    /**
     * @return array
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param array $url
     * @return Menu
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }


    /**
     * @return array
     */
    public function getSubItems()
    {
        return $this->sub_items;
    }

    /**
     * @param array $sub_items
     * @return Menu
     */
    public function setSubItems($sub_items)
    {
        $this->sub_items = $sub_items;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSubItems()
    {
        return (count($this->sub_items) != 0);
    }

    /**
     * @param $index|name
     * @return mixed|null
     */
    public function getItem($index)
    {
        if(is_numeric($index)) {
            if($index < count($this->sub_items)) {
                return $this->sub_items[$index];
            }
        } else {
            foreach ($this->sub_items as $menu) {
                if($menu->getName() == $index) {
                    return $menu;
                }
            }
        }

        return null;
    }

    public function addItem(Menu $subMenu, $position = Menu::MENU_POSITION_LAST, $position_reference = null)
    {
        if($position == Menu::MENU_POSITION_FIRST) {
            array_unshift($this->sub_items, $subMenu);
        } else if($position == Menu::MENU_POSITION_LAST) {
            array_push($this->sub_items, $subMenu);
        } else if($position == Menu::MENU_POSITION_BEFORE|| $position == Menu::MENU_POSITION_AFTER) {
            if($position_reference) {
                $index_position = null;
                array_walk($this->sub_items, function($item, $index) use ($position_reference, &$index_position) {
                    if($item->getName() == $position_reference) {
                        $index_position = $index;
                    }
                });
                if($index_position!==null) {
                    $slice_offset = ($position == Menu::MENU_POSITION_BEFORE? $index_position : $index_position + 1);
                    $this->sub_items = array_merge(
                        array_slice($this->sub_items, 0, $slice_offset),
                        [$subMenu],
                        array_slice($this->sub_items, $slice_offset)
                    );
                } else {
                    array_push($this->sub_items, $subMenu);
                }
            } else {
                throw new \Exception('No position_reference.');
            }
        }
        return $this;
    }

    public function getElementByName($name, Menu $menu) {
        if($name == $menu->getName()) {
            return $menu;
        } else {
            /**
             * @var string $key
             * @var Menu $value
             */
            foreach ($menu->getSubItems() as $key => $value) {
                if($value->getName() == $name){
                    return $value;
                } else {
                    if(($_menu = $this->getElementByName($name, $value))!==null) {
                        return $_menu;
                    }
                }
            }
        }
        return null;
    }
}