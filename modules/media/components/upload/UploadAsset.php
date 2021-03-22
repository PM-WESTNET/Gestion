<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\media\components\upload;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UploadAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/media/components/upload/assets';
    public $css = [
        'jquery.fileupload.css'
    ];
    public $js = [
        'UploadWidget.js',
        //Bower??
        'jquery.fileupload.js',
        'jquery.iframe-transport.js'
    ];
    public $depends = [
        'yii\jui\JuiAsset'
    ];
    public $publishOptions = [
        'forceCopy'=>true,
    ];
}
