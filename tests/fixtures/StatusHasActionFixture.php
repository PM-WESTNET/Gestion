<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:49
 */

namespace app\tests\fixtures;

use app\modules\ticket\models\StatusHasAction;
use yii\test\ActiveFixture;

class StatusHasActionFixture extends ActiveFixture
{

    public $modelClass = StatusHasAction::class;

    public $depends = [
        TicketStatusFixture::class,
        ActionFixture::class,
        AgendaTaskTypeFixture::class,
        AgendaTaskStatusFixture::class,
        AgendaCategoryFixture::class
    ];

    public $db = 'dbticket';
}