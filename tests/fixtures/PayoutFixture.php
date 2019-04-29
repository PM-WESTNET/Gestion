<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 3/04/19
 * Time: 15:09
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class PayoutFixture extends ActiveFixture
{

    public $modelClass= 'app\modules\westnet\ecopagos\models\Payout';

    public $depends = [
        CustomerFixture::class,
        EcopagoFixture::class,
        CashierFixture::class,
    ];
}