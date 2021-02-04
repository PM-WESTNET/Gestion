<?php

namespace app\tests\fixtures;

use app\modules\agenda\models\TaskType;
use yii\test\ActiveFixture;

class AgendaTaskTypeFixture extends ActiveFixture
{

    public $modelClass = TaskType::class;
    public $dataFile = '@app/tests/fixtures/data/agenda_task_type.php';

    public $depends = [
    ];

    public $db = 'dbagenda';
}