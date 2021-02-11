<?php

namespace app\tests\fixtures;

use app\modules\mobileapp\v1\models\MobilePushHasUserApp;
use yii\test\ActiveFixture;

class MobilePushHasUserAppFixture extends ActiveFixture
{
    public $modelClass = MobilePushHasUserApp::class;

    public $depends = [
        MobilePushFixture::class,
        UserAppFixture::class,
        
    ];
}