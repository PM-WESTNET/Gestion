<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 18/07/19
 * Time: 16:51
 */

namespace app\tests\fixtures;


use app\modules\sale\modules\contract\models\ContractDetail;
use yii\test\ActiveFixture;

class ContractDetailFixture extends ActiveFixture
{

    public $modelClass = ContractDetail::class;
    public $depends = [
        ContractFixture::class,
        ProductFixture::class
    ];

}