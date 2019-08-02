<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 22/07/19
 * Time: 11:57
 */

namespace app\tests\fixtures;


use app\modules\westnet\models\ConnectionForcedHistorial;
use yii\test\ActiveFixture;

class ConnectionForcedHistorialFixture extends ActiveFixture
{

    public $modelClass = ConnectionForcedHistorial::class;
    public $depends = [
        ConnectionFixture::class,
        UserFixture::class,
    ];
}