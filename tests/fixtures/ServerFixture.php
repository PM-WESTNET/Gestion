<?php

namespace app\tests\fixtures;

use app\modules\westnet\models\Server;
use yii\test\ActiveFixture;

class ServerFixture extends ActiveFixture
{

    public $modelClass = Server::class;

    public $depends = [
    ];
}