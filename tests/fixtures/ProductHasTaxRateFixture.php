<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 14/03/19
 * Time: 16:59
 */

namespace app\tests\fixtures;


use app\modules\sale\models\ProductHasTaxRate;
use yii\test\ActiveFixture;
use app\tests\fixtures\TaxRateFixture;

class ProductHasTaxRateFixture extends ActiveFixture
{

    public $modelClass = ProductHasTaxRate::class;
    public $depends = [
        ProductFixture::class,
        TaxRateFixture::class
    ];

}