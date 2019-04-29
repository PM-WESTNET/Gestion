<?php

namespace app\modules\mailing;


use app\components\menu\Menu;
use app\components\module\BaseModule;

class MailingModule extends BaseModule
{
    public $controllerNamespace = 'app\modules\mailing\controllers';

    public function init()
    {
        parent::init();

        $this->registerTranslations();
        $this->loadParams();
    }

    /**
     * @return Menu
     */
    public function getMenu(Menu $menu)
    {
        $menu->
        $_menu = (new Menu(Menu::MENU_TYPE_ITEM))
            ->setName('mailing')
            ->setLabel(self::t('Mailing Configuration'))
            ->setUrl(['/mailing/email-transport/index'])
        ;
        $menu->addItem($_menu, Menu::MENU_POSITION_LAST);
        return $menu;
    }

    public function getDependencies()
    {
        return [
        ];
    }
}
