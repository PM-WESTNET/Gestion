<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\ticket\models\Color;

class ColorFixture extends ActiveFixture
{

    public $modelClass = Color::class;

    public $depends = [
    ];

    public $db = 'dbticket';
}