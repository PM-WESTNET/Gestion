<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\accounting\assets;

use yii\web\AssetBundle;

/**
 * @author Carlos Garcia
  */
class JsTreeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/jstree/themes/default/style.min.css',
    ];
    public $js = [
        'js/jstree/jstree.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
