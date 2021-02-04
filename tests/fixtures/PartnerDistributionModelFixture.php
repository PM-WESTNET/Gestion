<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class PartnerDistributionModelFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\partner\models\PartnerDistributionModel';

    public $depends = [
        CompanyFixture::class,
    ];
}