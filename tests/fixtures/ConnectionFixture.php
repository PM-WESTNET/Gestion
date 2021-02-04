<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 14/03/19
 * Time: 13:19
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class ConnectionFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\westnet\models\Connection';
    public $depends = [
       ContractFixture::class,
       NodeFixture::class,
       ServerFixture::class
    ];
}