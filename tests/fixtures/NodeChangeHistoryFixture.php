<?php

namespace app\tests\fixtures;

use app\modules\westnet\models\NodeChangeHistory;
use yii\test\ActiveFixture;

class NodeChangeHistoryFixture extends ActiveFixture
{

    public $modelClass = NodeChangeHistory::class;
    public $depends = [
        NodeFixture::class,
        NodeChangeProcessFixture::class
    ];
}