<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/03/19
 * Time: 11:02
 */

namespace app\tests\fixtures;

use app\modules\ticket\models\Schema;
use yii\test\ActiveFixture;

class SchemaFixture extends ActiveFixture
{

    public $modelClass = Schema::class;
    public $dataFile = '@app/tests/fixtures/data/schema.php';

    public $depends = [
    ];

    public $db = 'dbticket';
}