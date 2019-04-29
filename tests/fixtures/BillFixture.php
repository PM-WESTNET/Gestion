<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 10:06
 */

namespace app\tests\fixtures;


use app\modules\sale\models\Bill;
use yii\test\ActiveFixture;

class BillFixture extends ActiveFixture
{
    public $modelClass = Bill::class;
    public $depends = [
        PartnerDistributionModelFixture::class,
        CustomerFixture::class,
        UserFixture::class,
        CurrencyFixture::class,
    ];
}