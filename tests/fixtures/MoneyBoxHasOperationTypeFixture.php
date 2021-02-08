<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 27/02/19
 * Time: 16:52
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class MoneyBoxHasOperationTypeFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\accounting\models\MoneyBoxHasOperationType';
    public $depends = [
        'operation_type' => [
            'class' => OperationTypeFixture::class,
        ],
        'account' => [
            'class' => AccountFixture::class
        ],
        'money_box' => [
            'class' => MoneyBoxFixture::class
        ],
        'money_box_account' =>[
            'class' => MoneyBoxAccountFixture::class
        ]
    ];
}