<?php

namespace app\tests\fixtures;

use app\modules\westnet\models\Node;
use yii\test\ActiveFixture;

class NodeFixture extends ActiveFixture
{

    public $modelClass = Node::class;

    public $depends = [
        ZoneFixture::class,
        CompanyFixture::class,
        ServerFixture::class
    ];
}