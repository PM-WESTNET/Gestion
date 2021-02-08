<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 18/03/19
 * Time: 11:38
 */

namespace app\assets;


use yii\web\AssetBundle;

class BootBoxAsset extends AssetBundle
{

    public $sourcePath = '@app/assets/bootbox';

    public $js = [
        'bootbox.min.js'
    ];

    public $depends = [
        AppAsset::class
    ];
}