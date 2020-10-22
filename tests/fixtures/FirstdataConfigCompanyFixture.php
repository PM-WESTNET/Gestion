<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;


class FirstdataConfigCompanyFixture extends ActiveFixture {
    
    public $modelClass = FirstdataConfigCompanyFixture::class;

    public $depends = [
        CompanyFixture::class
    ];
}

