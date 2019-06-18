<?php

namespace app\tests\fixtures;

use app\modules\accounting\models\Resume;
use yii\test\ActiveFixture;

class ResumeFixture extends ActiveFixture
{

    public $modelClass = Resume::class;

    public $depends = [
        MoneyBoxAccountFixture::class,
        CompanyFixture::class
    ];
}