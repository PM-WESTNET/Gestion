<?php

namespace app\tests\fixtures;

use app\modules\ticket\models\Category;
use yii\test\ActiveFixture;

class TicketCategoryFixture extends ActiveFixture
{

    public $modelClass = Category::class;
    public $dataFile = '@app/tests/fixtures/data/ticket_category.php';

    public $depends = [
        SchemaFixture::class,
        UserFixture::class,
    ];

    public $db = 'dbticket';
}