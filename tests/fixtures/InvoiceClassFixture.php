<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 22/02/19
 * Time: 18:29
 */
namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\sale\models\InvoiceClass;

class InvoiceClassFixture extends ActiveFixture
{
    public $modelClass = InvoiceClass::class;

    public $depends = [
    ];
}
