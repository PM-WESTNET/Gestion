<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 14/03/19
 * Time: 13:19
 */

namespace app\tests\fixtures;


use app\modules\westnet\models\Connection;
use yii\test\ActiveFixture;

class ConnectionFixture extends ActiveFixture
{

    public $modelClass = Connection::class;
    public $depends = [
       ContractFixture::class,
       NodeFixture::class,
       ServerFixture::class
    ];
}