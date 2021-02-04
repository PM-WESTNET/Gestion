<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 10:06
 */

namespace app\tests\fixtures;


use app\modules\sale\models\bills\Bill;
use yii\test\ActiveFixture;

class BillDetailFixture extends ActiveFixture
{

    public $modelClass = Bill::class;
    public $depends = [
        BillFixture::class,
        UnitFixture::class,
    ];

}