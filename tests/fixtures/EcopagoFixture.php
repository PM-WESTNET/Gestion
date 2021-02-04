<?php

namespace app\tests\fixtures;

use app\tests\fixtures\AccountFixture;
use yii\test\ActiveFixture;
use app\modules\westnet\ecopagos\models\Ecopago;

class EcopagoFixture extends ActiveFixture
{

    public $modelClass = Ecopago::class;

    public $depends = [
        StatusFixture::class,
        AccountFixture::class,
    ];

    public $db = 'dbecopago';
}