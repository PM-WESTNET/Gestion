<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 14/03/19
 * Time: 16:59
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class ProductPriceFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\sale\models\ProductPrice';
    public $depends = [
        ProductFixture::class
    ];

}