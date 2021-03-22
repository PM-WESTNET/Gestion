<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:53
 */

namespace app\tests\fixtures;

use app\modules\ticket\models\Action;
use yii\test\ActiveFixture;

class ActionFixture extends ActiveFixture
{

    public $modelClass = Action::class;

    public $depends = [
        TicketStatusFixture::class,
    ];

    public $db = 'dbticket';
}