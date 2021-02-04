<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 21/11/16
 * Time: 8:32
 */

namespace app\components\menu;

/**
 * Interface MenuRenderInterface
 * @package quoma\core\menu
 */
interface MenuRenderInterface
{
    /**
     * Renderiza el menu
     * @param Menu $menu
     * @return mixed
     */
    public function render(Menu $menu);
}