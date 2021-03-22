<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 11:49
 */

namespace app\tests\fixtures;


use app\modules\checkout\models\PaymentItem;
use yii\test\ActiveFixture;

class PaymentItemFixture extends ActiveFixture
{
    public $modelClass = PaymentItem::class;

    public $depends = [
      PaymentFixture::class,
      PaymentMethodFixture::class,
    ];


}