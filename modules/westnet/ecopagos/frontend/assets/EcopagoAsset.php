<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\westnet\ecopagos\frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class EcopagoAsset extends AssetBundle {

    public $sourcePath = '@app/modules/westnet/ecopagos/frontend/web/assets';
    
    public $css = [
        'css/frontend.css'
    ];
    public $js = [
        'js/BatchClosure.js',
        'js/Payout.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}
