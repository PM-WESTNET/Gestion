<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 21/11/16
 * Time: 16:22
 */

namespace app\components\menu\impl;

use quoma\core\menu\Menu;
use quoma\core\menu\MenuRenderInterface;

/**
 * Class NavMenuRender
 * Clase responsable de crear los items que tiene que renderizar el NavMenu.
 *
 * @package quoma\core\menu\impl
 */
class NavMenuRender implements  MenuRenderInterface
{
    /**
     * Crea el array de menu para renderizar.
     *
     * @param mixed $menu
     * @return array
     */
    public function render(Menu $menu, $isGhostMenu = false)
    {
        $item = [];
        // Seguramente es el menu padre o algo asi
        if($menu->getType() == null) {
            if($menu->hasSubItems()) {
                $items = [];
                foreach ($menu->getSubItems() as $subItem) {
                    $items[] = $this->render($subItem, $isGhostMenu);
                }
                $item  = $items;
            }
        } else if($menu->getType() == Menu::MENU_TYPE_ROOT || ($menu->getType() == Menu::MENU_TYPE_ITEM && $menu->hasSubItems() ) ) {
            $items = [];
            if($menu->hasSubItems()) {
                foreach ($menu->getSubItems() as $subItem) {
                    $items[] = $this->render($subItem, $isGhostMenu);
                }
            }
            $item['label'] = $menu->getLabel();
            if (!$isGhostMenu) {
                $item['visible'] = $menu->isVisible();
            }
            if(!empty($menu->getUrl())) {
                $item['url'] = $menu->getUrl();
            }
            if(!empty($menu->getExtraData())) {
                $item= array_merge($item,  $menu->getExtraData());
            }

            if(!empty($items)) {
                $item['items'] = $items;
            }

        } else if($menu->getType() == Menu::MENU_TYPE_ITEM) {
            $item['label'] = $menu->getLabel();
            if (!$isGhostMenu) {
                $item['visible'] = $menu->isVisible();
            }
            if(!empty($menu->getUrl())) {
                $item['url'] = $menu->getUrl();
            }
            if(!empty($menu->getExtraData())) {
                $item = array_merge($item,  $menu->getExtraData());
            }

        } else if($menu->getType() == Menu::MENU_TYPE_DIVIDER) {
            $item = ['label' => '<li class="divider"></li>'];
        }
        return $item;

    }
}