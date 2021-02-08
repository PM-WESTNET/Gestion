<?php

namespace app\tests\fixtures;

use app\modules\ticket\models\Status;
use yii\test\ActiveFixture;

class TicketStatusFixture extends ActiveFixture
{

    public $modelClass = Status::class;
    public $dataFile = '@app/tests/fixtures/data/ticket_status.php';

    public $depends = [
        ColorFixture::class
    ];

    public $db = 'dbticket';
}