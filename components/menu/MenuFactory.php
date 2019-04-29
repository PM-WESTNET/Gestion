<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 21/11/16
 * Time: 16:46
 */

namespace app\components\menu;


use app\components\menu\impl\NavMenuRender;

class MenuFactory
{
    const RENDERER_NAV = 'renderer_nav';

    /** @var  MenuFactory */
    private static $instance;

    /**
     * @return MenuFactory
     */
    public static function getInstance()
    {
        if(self::$instance===null) {
            self::$instance = new MenuFactory();
        }

        return self::$instance;
    }

    public function getRenderer($renderer)
    {
        if($renderer == MenuFactory::RENDERER_NAV) {
            return new NavMenuRender();
        }
    }
}