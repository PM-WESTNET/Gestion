<?php

namespace app\tests\fixtures;

use app\modules\westnet\models\NodeChangeProcess;
use yii\test\ActiveFixture;

class NodeChangeProcessFixture extends ActiveFixture
{

    public $modelClass = NodeChangeProcess::class;

    public $depends = [
        NodeFixture::class,
        UserFixture::class
    ];
}