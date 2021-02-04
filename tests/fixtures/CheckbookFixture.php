<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 23/08/19
 * Time: 15:40
 */

namespace app\tests\fixtures;


use app\modules\paycheck\models\Checkbook;
use yii\test\ActiveFixture;

class CheckbookFixture extends ActiveFixture
{

    public $modelClass = Checkbook::class;
    public $depends = [
        MoneyBoxAccountFixture::class
    ];
}