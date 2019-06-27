<?php

namespace app\tests\fixtures;

use app\modules\agenda\models\Category;
use yii\test\ActiveFixture;

class AgendaCategoryFixture extends ActiveFixture
{

    public $modelClass = Category::class;
    public $dataFile = '@app/tests/fixtures/data/agenda_category.php';

    public $depends = [
    ];

    public $db = 'dbagenda';
}