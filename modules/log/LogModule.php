<?php

namespace app\modules\log;

use Yii;
use app\components\module\BaseModule;
use app\components\menu\Menu;

class LogModule extends BaseModule
{

    public $controllerNamespace = 'app\modules\log\controllers';

    public function init()
    {
        parent::init();

        $this->registerTranslations();
    }

    /**
     * @return Menu
     */
    public function getMenu(Menu $menu)
    {
        $_menu = (new Menu(Menu::MENU_TYPE_ITEM))->setLabel(Yii::t('app', 'Logs'))->setUrl(['/log'])->setVisible(Yii::$app->user->isSuperadmin);

        $menu->addItem($_menu, Menu::MENU_POSITION_FIRST);

        return $_menu;
    }

    public function getDependencies()
    {
        return [];
    }

    public function registerTranslations()
    {

        foreach (Yii::$app->getModules() as $id => $module) {
            if (is_array($module)) {
                $class = new \ReflectionClass($module['class']);

                $basePath = dirname($class->getFileName()) . '/messages';
                if (file_exists($basePath)) {

                    if (file_exists($basePath . '/' . Yii::$app->language . "/$id-log.php")) {
                        \Yii::$app->i18n->translations[$id . '-log'] = [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'basePath' => $basePath,
                            'fileMap' => [
                                $id . "-log" => $id . '-log.php',
                            ],
                        ];
                    }
                }
            }
        }
    }

}
