<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:29
 */

namespace app\tests\fixtures;

use app\modules\ticket\models\Ticket;
use yii\test\ActiveFixture;

class TicketFixture extends ActiveFixture
{

    public $modelClass = Ticket::class;

    public $depends = [
        TicketStatusFixture::class,
        CustomerFixture::class,
        ColorFixture::class,
        TicketCategoryFixture::class,
        UserFixture::class
    ];

    public $db = 'dbticket';
}