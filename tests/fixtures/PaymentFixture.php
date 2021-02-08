<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 11:44
 */

namespace app\tests\fixtures;


use app\modules\checkout\models\Payment;
use yii\test\ActiveFixture;

class PaymentFixture extends ActiveFixture
{
    public $modelClass = Payment::class;
    public $depends = [
        CustomerFixture::class,
        PartnerDistributionModelFixture::class
    ];

}