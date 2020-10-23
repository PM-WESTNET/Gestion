<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\firstdata\models\FirstdataCompanyConfig;

class FirstdataConfigCompanyFixture extends ActiveFixture {
    
    public $modelClass = FirstdataCompanyConfig::class;

    public $depends = [
        CompanyFixture::class
    ];
}

