<?php

namespace app\tests\fixtures;

use app\modules\agenda\models\Status;
use yii\test\ActiveFixture;

class AgendaTaskStatusFixture extends ActiveFixture
{

    public $modelClass = Status::class;
    public $dataFile = '@app/tests/fixtures/data/agenda_task_status.php';

    public $depends = [
    ];

    public $db = 'dbagenda';
}