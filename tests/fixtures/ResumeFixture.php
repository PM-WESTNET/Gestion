<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 27/02/19
 * Time: 17:07
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class ResumeFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\accounting\models\Resume';
    public $depends = [
        CompanyFixture::class,
        MoneyBoxAccountFixture::class
    ];

}