<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 14/03/19
 * Time: 13:07
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class ContractFixture extends ActiveFixture
{
    public $modelClass= 'app\modules\sale\modules\contract\models\Contract';

    public $depends= [
        AddressFixture::class,
        CustomerFixture::class,
        VendorFixture::class
    ];

}