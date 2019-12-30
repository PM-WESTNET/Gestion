<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 17/12/19
 * Time: 15:30
 */

namespace app\tests\fixtures;


use app\modules\accounting\models\AccountMovementItem;
use yii\test\ActiveFixture;

class AccountMovementItemFixture extends ActiveFixture
{
    public $modelClass = AccountMovementItem::class;
    public $depends = [
        AccountMovementFixture::class,
        AccountFixture::class
    ];

}