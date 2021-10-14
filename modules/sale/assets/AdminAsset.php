<?php
namespace app\modules\sale\assets;

use yii\web\AssetBundle;
 

class AdminAsset extends AssetBundle {

    // The directory that contains the source asset files for this asset bundle

    public $sourcePath = '@app/modules/sale/web';

    // List of CSS files that this bundle contains
    public $css = [
    	'css/sale-bill-pdf.css',
        
    ];

}