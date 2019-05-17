<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 14/03/19
 * Time: 13:07
 */

namespace app\tests\fixtures;


use app\modules\cobrodigital\models\PaymentCardFile;
use yii\test\ActiveFixture;

class PaymentCardFileFixture extends ActiveFixture
{
    public $modelClass = PaymentCardFile::class;

    public $depends = [
    ];

}