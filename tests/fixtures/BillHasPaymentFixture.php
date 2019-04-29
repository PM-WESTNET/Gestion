<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 12:15
 */

namespace app\tests\fixtures;


use app\modules\checkout\models\BillHasPayment;
use yii\test\ActiveFixture;

class BillHasPaymentFixture extends ActiveFixture
{

    public $modelClass= BillHasPayment::class;
    public $depends = [
        BillFixture::class,
        PaymentFixture::class
    ];

}