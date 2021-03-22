<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 23/08/19
 * Time: 15:42
 */

namespace app\tests\fixtures;


use app\modules\paycheck\models\Paycheck;
use yii\test\ActiveFixture;

class PaycheckFixture extends ActiveFixture
{

    public $modelClass = Paycheck::class;
    public $depends = [
        MoneyBoxAccountFixture::class,
        MoneyBoxFixture::class,
        CheckbookFixture::class
    ];
}