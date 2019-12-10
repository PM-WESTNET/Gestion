<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 27/02/19
 * Time: 16:58
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class ResumeItemFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\accounting\models\ResumeItem';
    public $depends = [
        'money_box_has_operation_type' => [
            'class' => MoneyBoxHasOperationTypeFixture::class
        ],
        'resume' => [
            'class' => ResumeFixture::class
        ]
    ];

}